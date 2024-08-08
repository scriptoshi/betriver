<?php

namespace App\Support;

use App\Http\Resources\League;
use App\Models\Game;
use Illuminate\Support\Facades\File;
use Watson\Sitemap\Facades\Sitemap;

class Site
{
    public static function generate()
    {
        Sitemap::addTag(url('/'), now(), 'daily', '1');
        Sitemap::addTag(route('games.index'), now(), 'daily', '1');
        $games = Game::latest()->take(settings('sitemap_games', 100))->get();
        $games->each(function ($game) {
            Sitemap::addTag(route('games.show', ['sport' => $game->sport, 'games' => $game->name]), $game->created_at, 'weekly', '0.9');
        });
        Sitemap::addTag(route('leagues.index'), now(), 'daily', '1');
        $leagues = League::latest()->take(settings('sitemap_leagues', 100))->get();
        $leagues->each(function ($league) {
            Sitemap::addTag(route('leagues.show', ['sport' => $league->sport, 'games' => $league->name]), $league->created_at, 'weekly', '0.9');
        });
        //create
        $xml = Sitemap::render();
        file_put_contents(public_path('sitemap.xml'), $xml);
    }
    /**
     * remove the site map
     */
    public static function destroy(): bool
    {

        if (File::exists(public_path('sitemap.xml')))
            return File::delete(public_path('sitemap.xml'));
        return false;
    }
}
