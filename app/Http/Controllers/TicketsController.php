<?php

namespace App\Http\Controllers;

use App\Actions\Random;
use App\Enums\StakeType;
use App\Enums\TicketStatus;
use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ticket as TicketResource;
use App\Models\Game;
use App\Models\Odd;
use App\Models\Ticket;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class TicketsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Ticket::query()->with(['user', 'wagers']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('uid', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('payout', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->orWhereHas('wagers', function ($query) use ($keyword) {
                    $query->where('game_info', 'LIKE', "%$keyword%")
                        ->orWhere('bet_info', 'LIKE', "%$keyword%")
                        ->orWhere('market_info', 'LIKE', "%$keyword%");
                });;
        }
        $ticketsItems = $query->latest()->paginate($perPage);
        $tickets = TicketResource::collection($ticketsItems);
        return Inertia::render('Tickets/Index', compact('tickets'));
    }


    /**
     * Store a newly created ticket in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'wagers' => ['array', 'required'],
            'stake' => ['required', 'numeric'],
            'wagers.*.odd_id' => 'required|exists:odds,id',
            'wagers.*.game' => 'required|string', // game name
            'wagers.*.market' => 'required|string', // market name
            'wagers.*.bet' => 'required|string', // bet name
        ]);

        $ticket = new Ticket;
        $ticket->user_id = $request->user()->id;
        $ticket->uid =  Random::generate(16);
        $ticket->amount = $request->stake;
        $ticket->payout = 0;
        $ticket->total_odds = 0;
        $ticket->type  = StakeType::BACK;
        $ticket->status = TicketStatus::PENDING;
        $ticket->won = false;
        $ticket->is_withdrawn = false;
        $odds = Odd::query()
            ->findMany(
                collect($request->wagers)
                    ->pluck('odd_id')
                    ->all()
            )
            ->keyBy('id');
        $games = Game::query()
            ->findMany(
                $odds
                    ->pluck('game_id')
                    ->all()
            )
            ->keyBy('id');
        DB::beginTransaction();
        try {
            $ticket->save();
            foreach ($request->wagers as $wager) {
                $wager = (object)$wager;
                $odd =  $odds->get($wager->odd_id);
                $game =  $games->get($odd->game_id);
                $ticket->wagers()->create([
                    'bet_id' => $odd->bet_id,
                    'game_id' => $odd->game_id,
                    'odd_id' => $odd->id,
                    'odds' => $odd->odd,
                    'won' => false,
                    'sport' => $game->sport,
                    'game_info' => $wager->game,
                    'bet_info' => $wager->bet,
                    'market_info' => $wager->market,
                ]);
                $ticket->total_odds += $odd->odd;
            }
            $ticket->payout = $ticket->total_odds *  $ticket->amount;
            $ticket->save();
            $user = $ticket->user;
            $ticket->transactions()->create([
                'user_id' => $ticket->user_id,
                'description' => 'Bet Ticket #' . $ticket->uid,
                'amount' => $ticket->amount,
                'balance_before' => $user->balance,
                'action' => TransactionAction::DEBIT,
                'type' => TransactionType::TICKET
            ]);
            $user->decrement('balance', $ticket->amount);
            DB::commit();
            return back()->with('success', __('Bet placed successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['stake' => ['Error placing bet: :error', ['error' => $e->getMessage()]]]);
        }
    }
}
