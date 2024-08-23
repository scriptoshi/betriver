<?php

namespace App\Contracts;

interface GameStatus
{
    public function description(): string;

    public function gameState(): string;

    public function statusText(): string;

    public function ended(): bool;

    public function cancelled(): bool;

    public function finished(): bool;
}
