<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinuteSecondFormat implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the value matches the format "m:ss"
        if (!preg_match('/^(\d+):([0-5]\d)$/', $value, $matches)) {
            $fail('The :attribute must be in the format "minute:second" with seconds between 00 and 59.');
            return;
        }

        // $matches[1] contains minutes, $matches[2] contains seconds
        $minutes = (int)$matches[1];
        $seconds = (int)$matches[2];

        // Ensure seconds are between 0 and 59
        if ($seconds < 0 || $seconds > 59) {
            $fail('The :attribute must have seconds between 00 and 59.');
        }
    }
}
