<?php

namespace App\Actions;

use Illuminate\Support\Str;

class Random
{

    public static function generate($length = 13)
    {
        if (function_exists("openssl_random_pseudo_bytes"))
            return strtoupper(substr(bin2hex(openssl_random_pseudo_bytes(ceil($length / 2))), 0, $length));
        if (function_exists("random_bytes"))
            return strtoupper(substr(bin2hex(random_bytes(ceil($length / 2))), 0, $length));
        return Str::random($length);
    }

    public static function TwoFactorRecoveryCode()
    {
        return Str::random(10) . '-' . Str::random(10);
    }
}
