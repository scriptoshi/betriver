<?php

namespace App\Enums;

enum Segment: string
{
    case HOME = 'home';
    case AWAY = 'away';
    case BOTH = 'both';
    case EITHER = 'either';
}
