<?php

namespace App\Enums;

enum WagerStatus: string
{
    case PENDING = 'pending';
    case WINNER = 'winner';
    case LOST = 'lost';
    case CANCELLED = 'cancelled';
    case GAME_CANCELLED = 'game_cancelled';
    case REFUNDED = 'refunded';



    public function name(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::WINNER => 'Won',
            self::LOST => 'Lost',
            self::CANCELLED => 'Cancelled',
            self::GAME_CANCELLED => 'Game Cancelled',
            self::REFUNDED => 'Refunded'
        };
    }

    public static function getNames(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = [
                'name' => $case->name(),
                'value' => $case->value
            ];
            return $carry;
        }, []);
    }
}
