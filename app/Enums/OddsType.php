<?php

namespace App\Enums;

/**
 * Enum representing different types of betting odds.
 */
enum OddsType: string
{
    /**
     * Decimal odds, represented as a float greater than 1.
     * E.g., 2.50 means a successful bet of $100 returns $250 (including the stake).
     */
    case DECIMAL = 'decimal';

    /**
     * American odds, represented as a string with a '+' or '-' prefix.
     * Positive odds (e.g., +150) indicate the profit on a $100 stake.
     * Negative odds (e.g., -200) indicate the stake needed to profit $100.
     */
    case AMERICAN = 'american';

    /**
     * Percentage odds, represented as a float between 0 and 100.
     * Represents the implied probability of an event occurring.
     * E.g., 40.00 means a 40% chance of the event occurring.
     */
    case PERCENTAGE = 'percentage';

    /**
     * Get a human-readable description of the odds type.
     *
     * @return string The description of the odds type
     */
    public function description(): string
    {
        return match ($this) {
            self::DECIMAL => 'Decimal odds (e.g., 2.50)',
            self::AMERICAN => 'American odds (e.g., +150 or -200)',
            self::PERCENTAGE => 'Percentage odds (e.g., 40.00%)',
        };
    }
}
