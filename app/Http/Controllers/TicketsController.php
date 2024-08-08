<?php
namespace App\Http\Controllers;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ticket as TicketResource ;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Ticket::query()->with(['user','wagers']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
			->orWhere('uid', 'LIKE', "%$keyword%")
			->orWhere('amount', 'LIKE', "%$keyword%")
			->orWhere('payout', 'LIKE', "%$keyword%")
			->orWhere('total_odds', 'LIKE', "%$keyword%")
			->orWhere('status', 'LIKE', "%$keyword%")
			->orWhere('won', 'LIKE', "%$keyword%")
			->orWhere('is_withdrawn', 'LIKE', "%$keyword%");
        } 
        $ticketsItems = $query->latest()->paginate($perPage);
        $tickets = TicketResource::collection($ticketsItems);
        return Inertia::render('AdminTickets/Index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminTickets/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'user_id' => ['required','integer','exists:users,id'],
			'amount' => ['required','numeric'],
			'payout' => ['required','numeric'],
			'total_odds' => ['required','decimal'],
		]);
        $ticket = new Ticket;
        $ticket->user_id = $request->user_id;
		$ticket->uid = $request->uid;
		$ticket->amount = $request->amount;
		$ticket->payout = $request->payout;
		$ticket->total_odds = $request->total_odds;
		$ticket->status = $request->status;
		$ticket->won = $request->won;
		$ticket->is_withdrawn = $request->is_withdrawn;
		$ticket->save();
        
        return redirect()->route('tickets.index')->with('success', 'Ticket added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['user','wagers']);
        return Inertia::render('AdminTickets/Show', [
            'ticket'=> new TicketResource($ticket)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Ticket $ticket)
    {
        $ticket->load(['user','wagers']);
        return Inertia::render('AdminTickets/Edit', [
            'ticket'=> new TicketResource($ticket)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
			'user_id' => ['required','integer','exists:users,id'],
			'amount' => ['required','numeric'],
			'payout' => ['required','numeric'],
			'total_odds' => ['required','decimal'],
		]);
        
        $ticket->user_id = $request->user_id;
		$ticket->uid = $request->uid;
		$ticket->amount = $request->amount;
		$ticket->payout = $request->payout;
		$ticket->total_odds = $request->total_odds;
		$ticket->status = $request->status;
		$ticket->won = $request->won;
		$ticket->is_withdrawn = $request->is_withdrawn;
		$ticket->save();
        return back()->with('success', 'Ticket updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Ticket $ticket)
    {
        $ticket->active = !$ticket->active;
        $ticket->save();
        return back()->with('success', $ticket->active ? __(' :name Ticket Enabled !', ['name' => $ticket->name]) : __(' :name  Ticket Disabled!', ['name' => $ticket->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted!');
    }
}
