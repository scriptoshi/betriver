<?php

namespace App\Traits;

trait Handicaps
{


    public static function getHandicaps(): array
    {
        return collect(self::cases())
            ->filter(fn($case) => str($case->value)->startsWith('home_'))
            ->flatMap(function ($case) {
                $awayHandicap = str($case->value)
                    ->replace(['home_+', 'home_-'], ['away_-', 'away_+']);
                $key = str($case->value)->after('home_')->value();
                return [
                    'k' . $key    => [
                        $case->value,
                        self::from($awayHandicap)->value,
                    ]
                ];
            })
            ->all();
    }
}
