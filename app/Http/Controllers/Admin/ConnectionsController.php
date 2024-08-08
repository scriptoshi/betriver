<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConnectionProvider;
use App\Http\Controllers\Controller;
use App\Http\Resources\Connection as ConnectionResource;
use App\Models\Connection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConnectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Connection::query()->with(['user']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('provider', 'LIKE', "%$keyword%")
                ->orWhere('userId', 'LIKE', "%$keyword%");
        }
        $connectionsItems = $query->latest()->paginate($perPage);
        $connections = ConnectionResource::collection($connectionsItems);
        return Inertia::render('Admin/Connections/Index', compact('connections'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('Admin/Connections/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $connection = new Connection;
        $connection->user_id = $request->user_id;
        $connection->provider = $request->provider;
        $connection->userId = $request->userId;
        $connection->save();

        return redirect()->route('connections.index')->with('success', 'Connection added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Connection $connection)
    {
        $connection->load(['user']);
        return Inertia::render('Admin/Connections/Show', [
            'connection' => new ConnectionResource($connection)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Connection $connection)
    {
        $connection->load(['user']);
        return Inertia::render('Admin/Connections/Edit', [
            'connection' => new ConnectionResource($connection)
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
    public function update(Request $request, Connection $connection)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $connection->user_id = $request->user_id;
        $connection->provider = $request->provider;
        $connection->userId = $request->userId;
        $connection->save();
        return back()->with('success', 'Connection updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Connection $connection)
    {
        $connection->active = !$connection->active;
        $connection->save();
        return back()->with('success', $connection->active ? __(' :name Connection Enabled !', ['name' => $connection->name]) : __(' :name  Connection Disabled!', ['name' => $connection->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Connection $connection)
    {
        $connection->delete();
        return redirect()->route('connections.index')->with('success', 'Connection deleted!');
    }
}
