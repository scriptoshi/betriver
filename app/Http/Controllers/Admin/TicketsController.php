<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ticket as TicketResource;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $filter = null)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Ticket::query()
            ->with(['user', 'wagers.game']);
        if (!empty($keyword)) {
            $query->where('uid', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('payout', 'LIKE', "%$keyword%")
                ->orWhere('total_odds', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%");
            $query->orWhereHas('user', function (Builder $query) use ($keyword) {
                $query->where('email', 'LIKE', "%$keyword%");
            });
            $query->orWhereHas('wagers', function (Builder $query) use ($keyword) {
                $query->orWhere('bet_info', 'LIKE', "%$keyword%")
                    ->orWhere('market_info', 'LIKE', "%$keyword%")
                    ->orWhere('game_info', 'LIKE', "%$keyword%");
            });
        }
        if (!empty($filter)) {
            switch ($filter) {
                case 'winners':
                    $query->where('status', TicketStatus::WINNER);
                    break;
                case 'losers':
                    $query->where('status', TicketStatus::LOSER);
                    break;
                case 'pending':
                    $query->where('status', TicketStatus::PENDING);
                    break;
                default:
                    break;
            }
        }
        $ticketsItems = $query->latest()->paginate($perPage);
        $tickets = TicketResource::collection($ticketsItems);
        return Inertia::render('Admin/Tickets/Index', compact('tickets', 'filter'));
    }
}
