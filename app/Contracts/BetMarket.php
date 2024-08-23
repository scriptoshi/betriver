<?php

namespace App\Contracts;

use App\Models\Bet;
use App\Models\Game;
use App\Models\Team;

interface BetMarket
{
    public function oddsId();

    public function outcomes(): array;

    public function name(): string;

    public function won(Game $game, Bet $bet): bool;

    public static function seed(): void;
}
