<?php

namespace App\Enums\Races\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\MarketCategory;
use App\Enums\Rugby\Outcomes\RugbyAsianHandicapOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use App\Models\Team;
use Illuminate\Support\Str;

enum Winner: string implements BetMarket
{
    case GOLD = 'gold';
    case SILVER = 'silver';
    case BRONZE = 'bronze';

    public function oddsId(): ?int
    {
        return match ($this) {
            self::GOLD => null,
            self::SILVER => null,
            self::BRONZE => null,
        };
    }

    public function outcomes(): array
    {
        return RugbyAsianHandicapOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::GOLD => "Gold (First Position)",
            self::SILVER => "Silver (Second Position)",
            self::BRONZE => "Bronze (Third Position)",
        };
    }

    public function won(Game $game, Bet $team): bool
    {
        return false;
    }

    public function teamWon(Game $game, Team $team): bool
    {
        $gold = $game->result['gold'];
        $silver = $game->result['silver'];
        $bronze = $game->result['bronze'];
        return match ($this) {
            self::GOLD => $gold == $team->id,
            self::SILVER => $silver == $team->id,
            self::BRONZE => $bronze == $team->id,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::RACING
                ],
                [
                    'slug' => Str::slug(LeagueSport::RACING->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::WINNER,
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::RACING_WINNER,
                    'sport' => LeagueSport::RACING
                ]
            );
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
