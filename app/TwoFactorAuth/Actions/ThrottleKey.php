<?php

namespace App\TwoFactorAuth\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThrottleKey
{
    public static function from(Request $request)
    {
        return Str::transliterate(Str::lower($request->string('email')) . '|' . $request->ip());
    }
}
