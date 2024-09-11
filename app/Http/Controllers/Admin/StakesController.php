<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StakeStatus;
use App\Enums\StakeType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stake as StakeResource;
use App\Models\Stake;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StakesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $filter = null)
    {
        $keyword = $request->get('search');
        $status = $request->get('status');
        $perPage = 25;
        $query  = Stake::query()->with(['user']);
        if (!empty($keyword)) {
            $query->orWhere('bet_info', 'LIKE', "%$keyword%")
                ->orWhere('market_info', 'LIKE', "%$keyword%")
                ->orWhere('game_info', 'LIKE', "%$keyword%");
            $query->orWhereHas('user', function ($query) use ($keyword) {
                $query->where('email', 'LIKE', "%$keyword%");
            });
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($filter)) {
            switch ($filter) {
                case 'backs':
                    $query->where('type', StakeType::BACK);
                    break;
                case 'lays':
                    $query->where('type', StakeType::LAY);
                    break;
                case 'partial':
                    $query->where('status', StakeStatus::PARTIAL);
                    break;
                case 'matched':
                    $query->where('status', StakeStatus::MATCHED);
                    break;
                default:
                    break;
            }
        }
        $stakesItems = $query->latest()->paginate($perPage);
        $stakes = StakeResource::collection($stakesItems);

        return Inertia::render('Admin/Stakes/Index', [
            'stakes' => $stakes,
            'statuses' => StakeStatus::getNames(),
            'filter' => $filter
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Stake $stake)
    {
        $stake->delete();
        return redirect()->route('stakes.index')->with('success', 'Stake deleted!');
    }
}
