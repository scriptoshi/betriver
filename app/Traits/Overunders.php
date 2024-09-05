<?php

namespace App\Traits;

trait Overunders
{

    public static function getOverGroup(float $value): array
    {
        return [
            self::from("over_{$value}"),
            self::from("under_{$value}"),
        ];
    }

    public static function getOverUnders(): array
    {

        return collect(self::cases())
            ->filter(fn($case) => str($case->value)->startsWith('over_'))
            ->flatMap(function ($case) {
                $hc = abs(floatval(str($case->value)->after('over_')->value()));
                return ["k$hc" => static::getOverGroup($hc)];
            })
            ->all();
    }
}
