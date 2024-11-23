<?php

namespace App\Enums;

enum StakeStatus: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case MATCHED = 'matched';
    case WINNER = 'winner';
    case LOST = 'lost';
    case CANCELLED = 'cancelled';
    case GAME_CANCELLED = 'game_cancelled';
    case REFUNDED = 'refunded';
    case TRADE_OUT = 'trade_out';
    case TRADED_OUT = 'traded_out';

    public static function exposed()
    {
        return [
            static::PARTIAL,
            static::PENDING,
            static::MATCHED
        ];
    }

    public  function isExposed()
    {
        return match ($this) {
            self::PENDING,
            self::PARTIAL,
            self::MATCHED => true,
            default => false
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PARTIAL => 'Partially Matched',
            self::MATCHED => 'Fully Matched',
            self::WINNER => 'Won',
            self::LOST => 'Lost',
            self::CANCELLED => 'Cancelled',
            self::GAME_CANCELLED => 'Game Cancelled',
            self::REFUNDED => 'Refunded',
            self::TRADE_OUT => 'Trade Out',
            self::TRADED_OUT => 'Traded Out',
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
