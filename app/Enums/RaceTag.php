<?php

namespace App\Enums;

enum RaceTag: string
{

    case NEWS_POLITICS = 'politics';
    case HORSE_RACING = 'horse_racing';
    case CYCLING = 'cycling';
    case ATHLETICS = 'athletics';
    case HOUND_RACING = 'hound_racing';
    case MOTORSPORT = 'motorsport';
    case FORMULAR_ONE = 'f1';
    case GOAT_RACING = 'goat_racing';


    public function name(): string
    {
        return match ($this) {
            static::NEWS_POLITICS => 'News and Politics',
            static::HORSE_RACING => 'Horse Racing',
            static::CYCLING => 'Cycling',
            static::ATHLETICS => 'Athletics',
            static::HOUND_RACING => 'Hound Racing',
            static::MOTORSPORT => 'Motor Sport',
            static::FORMULAR_ONE => 'Formular One',
            static::GOAT_RACING => 'Goat Racing',
        };
    }
}
