<?php
namespace App\Http\Controllers;
use App\Enums\ScoreType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Score as ScoreResource ;
use App\Models\Score;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScoresController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Score::query()->with(['game']);
        if (!empty($keyword)) {
            $query->where('game_id', 'LIKE', "%$keyword%")
			->orWhere('type', 'LIKE', "%$keyword%")
			->orWhere('home', 'LIKE', "%$keyword%")
			->orWhere('away', 'LIKE', "%$keyword%");
        } 
        $scoresItems = $query->latest()->paginate($perPage);
        $scores = ScoreResource::collection($scoresItems);
        return Inertia::render('AdminScores/Index', compact('scores'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminScores/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'game_id' => ['required','integer','exists:games,id'],
			'type' => ['required','string',new Enum(ScoreType::class)],
			'home' => ['required','string'],
			'away' => ['required','string'],
		]);
        $score = new Score;
        $score->game_id = $request->game_id;
		$score->type = $request->type;
		$score->home = $request->home;
		$score->away = $request->away;
		$score->save();
        
        return redirect()->route('scores.index')->with('success', 'Score added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Score $score)
    {
        $score->load(['game']);
        return Inertia::render('AdminScores/Show', [
            'score'=> new ScoreResource($score)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Score $score)
    {
        $score->load(['game']);
        return Inertia::render('AdminScores/Edit', [
            'score'=> new ScoreResource($score)
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
    public function update(Request $request, Score $score)
    {
        $request->validate([
			'game_id' => ['required','integer','exists:games,id'],
			'type' => ['required','string',new Enum(ScoreType::class)],
			'home' => ['required','string'],
			'away' => ['required','string'],
		]);
        
        $score->game_id = $request->game_id;
		$score->type = $request->type;
		$score->home = $request->home;
		$score->away = $request->away;
		$score->save();
        return back()->with('success', 'Score updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Score $score)
    {
        $score->active = !$score->active;
        $score->save();
        return back()->with('success', $score->active ? __(' :name Score Enabled !', ['name' => $score->name]) : __(' :name  Score Disabled!', ['name' => $score->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Score $score)
    {
        $score->delete();
        return redirect()->route('scores.index')->with('success', 'Score deleted!');
    }
}
