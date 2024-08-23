<?php

namespace App\Http\Controllers;

use App\Enums\CommonLanguage;
use App\Enums\OddsType;
use App\Http\Controllers\Controller;
use App\Models\Favourite;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class FavouritesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string'],
        ]);
        Favourite::query()->updateOrCreate([
            'user_id' => $request->user()->id,
            'key' => $request->key
        ], ['key' => $request->key]);
        return back();
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function odds(Request $request)
    {
        $request->validate([
            'oddsType' => ['required', 'string', new Enum(OddsType::class)],
        ]);
        $user = $request->user();
        $user->odds_type = $request->oddsType;
        $user->save();
        return back();
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function lang(Request $request)
    {
        $request->validate([
            'lang' => ['required', 'string', new Enum(CommonLanguage::class)],
        ]);
        $user = $request->user();
        $user->lang = $request->lang;
        $user->save();
        return back();
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function hideBalance(Request $request)
    {
        $request->validate([
            'hideBalance' => ['required', 'boolean'],
        ]);
        $user = $request->user();
        $user->hide_balance = $request->hideBalance;
        $user->save();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, $favourite)
    {
        $request->user()->favourites()->where('key', $favourite)->delete();
        return back();
    }
}
