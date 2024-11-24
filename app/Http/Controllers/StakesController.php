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
use App\Events\GameChanged;
use App\Events\PriceChanged;
use App\Models\GameMarket;
use App\Http\Resources\StatStake;
use App\Models\Bet;
use App\Models\Game;
use App\Support\EventHydrant;
use App\Support\TradeManager;
use Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Str;

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
     * Store a  stake
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $min = settings('site.exchange_min_bet', 0.01);
        $request->validate([
            'stake' => "required|numeric|min:$min",
            'price' => 'required|numeric|min:1.01',
            'isLay' => ['required',  'boolean'],
            'isAsk' => ['required',  'boolean'],
            'bet_id' => 'required|exists:bets,id',
            'game_id' => 'required|exists:games,id',
            'game' => 'required|string',
            'market' => 'required|string',
            'bet' => 'required|string',
        ]);
        $type  = $request->isLay ? StakeType::LAY : StakeType::BACK;
        $liability =  $type === StakeType::LAY
            ? $request->stake * ($request->price - 1)
            : $request->stake;
        if ($request->user()->balance < $liability) {
            throw ValidationException::withMessages(['amount' => ['Low Balance']]);
        }
        $bet = Bet::find($request->bet_id);
        $game = Game::find($request->game_id);
        //ensure market is attached.
        $game->markets()->syncWithoutDetaching([$bet->market_id => ['uuid' => Str::uuid()]]);
        $stake = new Stake();
        $stake->user_id = $request->user()->id;
        $stake->bet_id = $bet->id;
        $stake->odds =  $request->price;
        $stake->amount =  $request->stake;
        $stake->game_id = $request->game_id;
        $stake->market_id = $bet->market_id;
        $stake->uid = Random::generate();
        $stake->type =  $type;
        $stake->filled =  0;
        $stake->sport =  $game->sport;
        $stake->status = StakeStatus::PENDING;
        $stake->unfilled = $request->stake;
        $stake->qty = $request->stake * $request->price;
        $stake->payout = 0;
        $stake->liability = $liability;
        $stake->game_info = $request->game;
        $stake->market_info = $request->market;
        $stake->bet_info = $request->bet;
        DB::beginTransaction();
        try {
            $stake->save();
            $stake = TradeManager::matchStake($stake);
            TradeManager::collectLiability($stake);
            DB::commit();
            $gameMarket = GameMarket::where('game_id', $stake->game_id)->where('market_id', $stake->market_id)->first();
            static::fireEvents($gameMarket);
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
            TradeManager::refundLiability($stake);
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
            DB::commit();
            $gameMarket = GameMarket::where('game_id', $stake->game_id)->where('market_id', $stake->market_id)->first();
            static::fireEvents($gameMarket);
            return back()->with('success', __('Stake cancelled successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error cancelling stake :error', ['error' => $e->getMessage()]));
        }
    }

    public function showTradeOut(Request $request, Stake $stake = null)
    {
        if (!$stake) return Inertia::render('Stakes/TradeOut');
        Gate::authorize('update', $stake);
        return Inertia::render('Stakes/TradeOut', [
            'stake' => function () use ($stake) {
                $stake->load('game');
                return new StakeResource($stake);
            },
            'odds' => function () use ($stake) {
                $rangeSize = (float) settings('odds_spread', 0.2);
                $rangeSize = $rangeSize < 0.001 ? 0.2 :  $rangeSize;
                $query = Stake::query()->where('type', $stake->type)
                    ->whereIn('status', [StakeStatus::PENDING->value, StakeStatus::PARTIAL->value])
                    ->where('unfilled', '>', 0)
                    ->where('market_id', $stake->market_id)
                    ->where('bet_id', $stake->bet_id)
                    ->where('game_id', $stake->game_id);
                if ($stake->type == StakeType::LAY) {
                    $query->select(
                        DB::raw("FLOOR(odds / $rangeSize) AS range_code"),
                        DB::raw('SUM(unfilled) AS amount'),
                        DB::raw('MAX(odds) as price')
                    )
                        ->groupBy(DB::raw("FLOOR(odds / $rangeSize)"))
                        ->latest(DB::raw('MAX(odds)'));
                } else {
                    $query->select(

                        DB::raw("FLOOR(odds / $rangeSize) AS range_code"),
                        DB::raw('SUM(unfilled) AS amount'),
                        DB::raw('MIN(odds) as price')
                    )
                        ->groupBy(DB::raw("FLOOR(odds / $rangeSize)"))
                        ->oldest(DB::raw('MAX(odds)'));
                }
                $markets = $query->take(3)->get();
                return  StatStake::collection($markets);
            }
        ]);
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
        if (!in_array($stake->status, [StakeStatus::MATCHED, StakeStatus::PARTIAL])) {
            return back()->with('error', __('Only matched or partially matched stakes can be traded out'));
        }
        $newOdds = $request->new_odds;
        DB::beginTransaction();
        try {
            if ($stake->status === StakeStatus::PARTIAL && $stake->unfilled > 0) {
                // Create a new stake the unfilled portion
                $unfilledStake = $stake->replicate();
                $unfilledStake->original_stake_id = $stake->id;
                $unfilledStake->amount = $stake->unfilled;
                $unfilledStake->uid =  Random::generate();
                $unfilledStake->filled = 0;
                $unfilledStake->unfilled = $stake->unfilled;
                $unfilledStake->status = StakeStatus::PENDING;
                $unfilledStake->liability = TradeManager::calculateLiability($stake->type, $unfilledStake->amount, $stake->odds);
                $unfilledStake->save();
                // Update the stake
                $stake->amount = $stake->filled;
                $stake->unfilled = 0;
                $stake->status = StakeStatus::MATCHED;
                $stake->liability = TradeManager::calculateLiability($stake->type, $stake->amount, $stake->odds);
                $stake->save();
            }
            $originalAmount = $stake->amount;
            $originalOdds = $stake->odds;
            // Calculate the trade-out amount
            $tradeOutAmount = TradeManager::calculateTradeOutAmount($stake->type, $originalAmount, $originalOdds, $newOdds);
            // Calculate profit/loss
            $profitLoss = TradeManager::calculateProfitLoss($stake->type, $originalAmount, $originalOdds, $tradeOutAmount, $newOdds);
            // Create a new opposing stake
            $newStakeType = $stake->type === StakeType::BACK ? StakeType::LAY : StakeType::BACK;
            $newStake = new Stake();
            $newStake->fill([
                'user_id' =>  $stake->user_id,
                'uid' => Random::generate(),
                'original_stake_id' => $stake->id,
                'game_id' => $stake->game_id,
                'bet_id' => $stake->bet_id,
                'market_id' => $stake->market_id,
                'amount' => $tradeOutAmount,
                'odds' => $newOdds,
                'type' => $newStakeType,
                'sport' =>  $stake->sport,
                'is_trade_out' => true,
                'status' => StakeStatus::PENDING,
                'unfilled' => $tradeOutAmount,
                'filled' => $tradeOutAmount,
                'profit_loss' => $profitLoss,
                'liability' => TradeManager::calculateLiability($newStakeType, $tradeOutAmount, $newOdds)
            ]);
            $newStake->save();
            // Handle profit/loss and liability
            $user = $stake->user;
            $newLiability = $newStake->liability;
            // Calculate additional exposure created
            $extraExposure = round($newLiability - $stake->liability, 2);
            if ($extraExposure > 0) { // increased Exposure
                // Check if user has enough balance
                if ($user->balance < $extraExposure) {
                    throw new \Exception('Insufficient balance for trade-out');
                }
                // Deduct additional liability from user's balance
                $user->decrement('balance', $extraExposure);
                $newStake->transactions()->create([
                    'user_id' => $user->id,
                    'description' => "Additional liability for trade-out of BET #" . $stake->uid,
                    'amount' => $extraExposure,
                    'balance_before' => $user->balance + $extraExposure,
                    'action' => TransactionAction::DEBIT,
                    'type' => TransactionType::TRADE_OUT_LIABILITY
                ]);
            } elseif ($extraExposure < 0) { // reduced Exposure
                $user->increment('balance', $extraExposure);
                $newStake->transactions()->create([
                    'user_id' => $user->id,
                    'description' => "Reduced liability on trade-out of BET #" . $stake->uid,
                    'amount' => abs($extraExposure),
                    'balance_before' => $user->balance + abs($extraExposure),
                    'action' => TransactionAction::CREDIT,
                    'type' => TransactionType::TRADE_OUT_EXPOSURE
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
            $gameMarket = GameMarket::where('game_id', $stake->game_id)->where('market_id', $stake->market_id)->first();
            static::fireEvents($gameMarket);
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


    private static function fireEvents(GameMarket $gameMarket)
    {
        $gameMarket->load(['market', 'game']);
        if ($gameMarket->market->is_default) {
            $gameResource = EventHydrant::hydrateGame($gameMarket->game, $gameMarket->market);
            GameChanged::dispatch($gameResource, $gameMarket->game->uuid);
        }
        $market = EventHydrant::hydrateMarket($gameMarket->game, $gameMarket->market);
        PriceChanged::dispatch($market, $gameMarket->channel());
    }
}
