<?php

namespace App\Enums\Handball\Outcomes;

enum OverUnderOutcome: string
{
    case OVER_395 = 'over_39.5';
    case UNDER_395 = 'under_39.5';
    case OVER_405 = 'over_40.5';
    case UNDER_405 = 'under_40.5';
    case OVER_415 = 'over_41.5';
    case UNDER_415 = 'under_41.5';
    case OVER_425 = 'over_42.5';
    case UNDER_425 = 'under_42.5';
    case OVER_435 = 'over_43.5';
    case UNDER_435 = 'under_43.5';
    case OVER_445 = 'over_44.5';
    case UNDER_445 = 'under_44.5';
    case OVER_455 = 'over_45.5';
    case UNDER_455 = 'under_45.5';
    case OVER_465 = 'over_46.5';
    case UNDER_465 = 'under_46.5';
    case OVER_475 = 'over_47.5';
    case UNDER_475 = 'under_47.5';
    case OVER_485 = 'over_48.5';
    case UNDER_485 = 'under_48.5';
    case OVER_495 = 'over_49.5';
    case UNDER_495 = 'under_49.5';
    case OVER_505 = 'over_50.5';
    case UNDER_505 = 'under_50.5';
    case OVER_515 = 'over_51.5';
    case UNDER_515 = 'under_51.5';

    public function name(): string
    {
        $type = str($this->value)->before('_')->ucfirst();
        $value = str($this->value)->after('_');
        return "{$type} {$value}";
    }

    public function type(): string
    {
        return str($this->value)->before('_');
    }

    public function threshold(): float
    {
        return (float) str($this->value)->after('_')->value();
    }

    public static function getOverUnderGroup(float $value): array
    {
        return [
            self::from("over_{$value}"),
            self::from("under_{$value}"),
        ];
    }
}
