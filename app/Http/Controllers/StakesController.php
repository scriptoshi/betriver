<?php

namespace App\Http\Controllers;

use App\Actions\Random;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stake as StakeResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Stake;
use App\Enums\StakeType;
use App\Enums\StakeStatus;
use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Models\Bet;
use App\Support\TradeManager;
use Exception;
use Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class StakesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Stake::query()->with(['user', 'game', 'bet', 'maker_trades', 'taker_trades']);
        if (!empty($keyword)) {
            $query->where('slip_id', 'LIKE', "%$keyword%")
                ->orWhere('user_id', 'LIKE', "%$keyword%")
                ->orWhere('bet_id', 'LIKE', "%$keyword%")
                ->orWhere('game_id', 'LIKE', "%$keyword%")
                ->orWhere('uuid', 'LIKE', "%$keyword%")
                ->orWhere('scoreType', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('filled', 'LIKE', "%$keyword%")
                ->orWhere('unfilled', 'LIKE', "%$keyword%")
                ->orWhere('payout', 'LIKE', "%$keyword%")
                ->orWhere('odds', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->orWhere('won', 'LIKE', "%$keyword%")
                ->orWhere('is_withdrawn', 'LIKE', "%$keyword%")
                ->orWhere('allow_partial', 'LIKE', "%$keyword%");
        }
        $stakesItems = $query->latest()->paginate($perPage);
        $stakes = StakeResource::collection($stakesItems);
        return Inertia::render('AdminStakes/Index', compact('stakes'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminStakes/Create');
    }

    /**
     * Store a  stake
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $min = settings('site.exchange_min_bet', 0.01);
        $request->validate([
            'amount' => "required|numeric|min:$min",
            'odds' => 'required|numeric|min:1.01',
            'type' => ['required', 'string', new Enum(StakeType::class)],
            'bet_id' => 'required|exists:bets,id',
            'game_id' => 'required|exists:games,id',
        ]);
        $type  = StakeType::from($request->type);
        $liability =  $type === StakeType::LAY
            ? $request->amount * ($request->odds - 1)
            : $request->amount;
        if ($request->user()->balance < $liability) {
            throw ValidationException::withMessages(['amount' => ['Low Balance']]);
        }
        $bet = Bet::find($request->bet_id);
        $stake = new Stake();
        $stake->fill($request->only(['amount', 'odds', 'bet_id']));
        $stake->user_id = $request->user()->id;
        $stake->bet_type = (new Bet)->getMorphClass();
        $stake->game_id = $request->game_id;
        $stake->market_id = $bet->market_id;
        $stake->uid = Random::generate();
        $stake->type =  $type;
        $stake->status = StakeStatus::PENDING;
        $stake->unfilled = $request->amount;
        $stake->qty = $request->amount * $request->odds;
        $stake->payout = 0;
        $stake->liability = $liability;
        DB::beginTransaction();
        try {
            $stake->save();
            $stake = TradeManager::matchStake($stake);
            TradeManager::collectLiability($stake);
            DB::commit();
            return back()->with('success', __('Bet placed successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error placing bet: :error', ['error' => $e->getMessage()]));
        }
    }


    /**
     * Users can cancel partial or unmatched bets
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Stake $stake;
     * @return \Illuminate\Routing\Redirector
     */

    public function cancel(Request $request, Stake $stake)
    {
        Gate::authorize('update', $stake);
        $stake->load('user');
        // Check if the stake can be cancelled
        if (!in_array($stake->status, [StakeStatus::PENDING, StakeStatus::PARTIAL])) {
            return back()->with('error', __('This stake cannot be cancelled'));
        }
        DB::beginTransaction();
        try {
            // If the stake is partially matched, we need to handle the matched portion
            if ($stake->status === StakeStatus::PARTIAL && $stake->unfilled > 0) {
                // Create a new stake for the cancelled portion
                $cancelledStake = $stake->replicate();
                $cancelledStake->original_stake_id = $stake->id;
                $cancelledStake->amount = $stake->unfilled;
                $cancelledStake->filled = 0;
                $cancelledStake->unfilled = $stake->unfilled;
                $cancelledStake->status = StakeStatus::CANCELLED;
                $cancelledStake->liability = TradeManager::calculateLiability($stake->type, $stake->amount, $stake->odds); // refund
                $cancelledStake->save();
                // Update the stake
                $stake->amount = $stake->filled;
                $stake->unfilled = 0;
                $stake->status = StakeStatus::MATCHED;
                $stake->liability = TradeManager::calculateLiability($stake->type, $stake->amount, $stake->odds);
                $stake->save();
            } else {
                // For fully unmatched stakes, we can simply cancel the entire stake
                $stake->status = StakeStatus::CANCELLED;
                $stake->liability = 0;
                $stake->save();
            }
            TradeManager::refundLiability($stake);
            DB::commit();
            return back()->with('success', __('Stake cancelled successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error cancelling stake :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Users can tradeout matched bets
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Stake $stake;
     * @return \Illuminate\Routing\Redirector
     */

    public function tradeOut(Request $request, Stake $stake)
    {
        Gate::authorize('update', $stake);
        $request->validate([
            'new_odds' => 'required|numeric|min:1.01',
        ]);

        if ($stake->status !== StakeStatus::MATCHED) {
            return back()->with('error', __('This stake cannot be traded out'));
        }

        $newOdds = $request->new_odds;
        $originalAmount = $stake->amount;
        $originalOdds = $stake->odds;

        DB::beginTransaction();
        try {
            // Calculate the trade-out amount
            $tradeOutAmount = TradeManager::calculateTradeOutAmount($stake->type, $originalAmount, $originalOdds, $newOdds);
            // Calculate profit/loss
            $profitLoss = TradeManager::calculateProfitLoss($stake->type, $originalAmount, $originalOdds, $tradeOutAmount, $newOdds);
            // Create a new opposing stake
            $newStake = new Stake();
            $newStake->fill([
                'user_id' =>  $stake->user_id,
                'original_stake_id' => $stake->id,
                'game_id' => $stake->game_id,
                'bet_id' => $stake->bet_id,
                'market_id' => $stake->market_id,
                'bet_type' => $stake->bet_type,
                'amount' => $tradeOutAmount,
                'odds' => $newOdds,
                'type' => $stake->type === StakeType::BACK ? StakeType::LAY : StakeType::BACK,
                'is_trade_out' => true,
                'status' => StakeStatus::PENDING,
                'unfilled' => $tradeOutAmount,
                'profit_loss' => $profitLoss,
                'liability' => TradeManager::calculateLiability($newStake->type, $tradeOutAmount, $newOdds)
            ]);
            $newStake->save();
            // Handle profit/loss and liability
            $user = $stake->user;
            $newLiability = $newStake->liability;
            // Calculate additional exposure created
            $extraExposure = $newLiability - $stake->liability;
            if ($extraExposure > 0) { // increased Exposure
                // Check if user has enough balance
                if ($user->balance < $extraExposure) {
                    throw new \Exception('Insufficient balance for trade-out');
                }
                // Deduct additional liability from user's balance
                $user->decrement('balance', $extraExposure);
                $newStake->transactions()->create([
                    'user_id' => $user->id,
                    'description' => "Additional liability for trade-out of BET #" . $stake->id,
                    'amount' => $extraExposure,
                    'balance_before' => $user->balance + $extraExposure,
                    'action' => TransactionAction::DEBIT,
                    'type' => TransactionType::TRADE_OUT_LIABILITY
                ]);
            } elseif ($extraExposure < 0) { // reduced Exposure
                $user->increment('balance', $extraExposure);
                $newStake->transactions()->create([
                    'user_id' => $user->id,
                    'description' => "Reduced liability on trade-out of BET #" . $stake->id,
                    'amount' => $extraExposure,
                    'balance_before' => $user->balance + $extraExposure,
                    'action' => TransactionAction::CREDIT,
                    'type' => TransactionType::TRADE_OUT_LIABILITY
                ]);
            }
            // Attempt to match the new stake
            $newStake = TradeManager::matchStake($newStake);
            if ($newStake->unfilled > 0) {
                throw new \Exception(__('Insufficient Liquidity at given odds'));
            }
            $stake->is_traded_out = true;
            $stake->status = StakeStatus::TRADED_OUT;
            $stake->save();
            DB::commit();
            return back()->with('success', __('Trade-out stake created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error trading out stake: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Stake $stake)
    {
        $stake->load(['user', 'game', 'bet', 'maker_trades', 'taker_trades']);
        return Inertia::render('AdminStakes/Show', [
            'stake' => new StakeResource($stake)
        ]);
    }
}
