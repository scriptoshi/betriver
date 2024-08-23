<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Favourite;

use Illuminate\Http\Request;

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
