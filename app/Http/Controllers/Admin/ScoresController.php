<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ScoreType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Game as ResourcesGame;
use App\Models\Game;
use App\Models\Score;
use App\Rules\MinuteSecondFormat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class ScoresController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Game $game)
    {
        $game->load(['awayTeam', 'homeTeam']);
        $scores = $game->scores()
            ->get()
            ->map(fn(Score $score) => [...$score->toArray(), 'type' => $score->type])
            ->keyBy('type');
        $scores_data = collect($game->sport->scores())->map(function ($score) use ($game, $scores) {
            return [
                'name' => $score->name(),
                'game_id' => $game->id,
                'type' => $score->value,
                'home' => $scores[$score->value]['home'] ?? null,
                'away' => $scores[$score->value]['away'] ?? null,
            ];
        });
        return Inertia::render('Admin/Scores/Index', [
            'scores' => $scores_data,
            'game' => new ResourcesGame($game)
        ]);
    }


    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'type' => ['required', 'string', new Enum(ScoreType::class)],
            'home' => ['required', 'string'],
            'away' => ['required', 'string'],
        ]);
        Score::query()->updateOrCreate([
            'game_id' => $request->game_id,
            'type' => $request->type
        ], [
            'home' => $request->home,
            'away' => $request->away
        ]);
        return back()->with('success', 'Scores updated!');
    }
    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function mma(Request $request)
    {
        $request->validate([
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'winner' => ['required', 'integer', 'exists:teams,id'],
            'won_type' => ['required', 'string'],
            'round' => ['required', 'numeric'],
            'minute' => ['required', 'string', new MinuteSecondFormat],
            'ko_type' => ['nullable', 'string'],
            'sub_type' => ['nullable', 'string']
        ]);
        $result = $request->only(['won_type', 'round', 'minute', 'ko_type', 'sub_type']);
        $game = Game::findOrFail($request->game_id);
        $game->win_team_id = $request->winner;
        $game->result = $result;
        $game->save();
        return back()->with('success', 'Results updated!');
    }
}
