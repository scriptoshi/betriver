<?php

namespace App\Menus;

use App\Enums\LeagueSport;
use App\Http\Resources\Game;
use App\Models\League;
use Illuminate\Database\Eloquent\Builder;

class Football
{
    public static function menu()
    {
        $activeCounts = Game::getActiveCountsBySport();
        return [
            'name' => 'Soccer',
            'route' => "sports.index",
            'sport' => "football",
            'icon' => "Soccer",
            'count' =>  $activeCounts['football'] ?? 0,
            'submenu' => League::getGameCountsBySport(LeagueSport::FOOTBALL),
        ];
    }
}
