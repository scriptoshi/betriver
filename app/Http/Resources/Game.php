<?php



namespace App\Http\Resources;

use App\Enums\LeagueSport;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class Game extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $status = $this->sport->gameStatus($this->status);
        return [
            'uuid' => $this->uuid,
            'id' => $this->id,
            'uid' => md5($this->id),
            'slug' => $this->id . '/' . Str::slug($this->name),
            'league_id' => $this->league_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'win_team_id' => $this->win_team_id,
            'name' => $this->name,
            'traded' => $this->traded ?? 0,
            'liquidity' => $this->liquidity ?? 0,
            'volume' => $this->volume ?? 0,
            'startTime' => $this->startTime,
            'startTimeAgo' =>  $this->startTime->diffForHumans(),
            'endTime' => $this->endTime,
            'status' => $status->gameState() ?? null,
            'statusText' => $status?->statusText() ?? null,
            'statusTooltip' => $status?->description() ?? null,
            'state' => $status?->gameState() ?? null,
            'stateCancelled' => $status?->cancelled() ?? null,
            'stateFinished' => $status?->finished() ?? null,
            'stateEnded' => $status?->ended() ?? null,
            'finalScoreType' => $this->sport->finalScoreType(),
            'result' => $this->result,
            'rounds' => $this->rounds,
            'elapsed' => $this->elapsed,
            'sport' => $this->sport,
            'active' => $this->active,
            'has_odds' => $this->has_odds,
            'has_scores' => $this->has_scores || ($this->sport === LeagueSport::MMA && !is_null($this->result)),
            'closed' => $this->closed,
            'bets_matched' => $this->bets_fixed,
            /**
             * Query derived values
             * 
             */
            'traded' => $this->traded ?? 0,
            'marketsCount' => $this->marketsCount ?? 0,
            /**
             *  derived values
             */
            'startTimeFormatted' => $this->startTime->toDateTimeString(),
            'startTimeTs' => $this->startTime->timestamp,
            'startTimeGmt' => $this->startTime->format('l, H:i \G\M\TP'),
            'startsAt' => $this->startTime->format('H:i'),
            'isSoon' => $this->startTime->greaterThan(now()) && $this->startTime->lessThanOrEqualTo(now()->addHour()),
            'hasStarted' => now()->gt($this->startTime),
            'endTimeFormatted' => $this->endTime ? $this->endTime->toDateTimeString() : null,
            'hasEnded' =>  $this->closed || (!is_null($this->endTime) && now()->gt($this->endTime)),
            'isToday' => $this->startTime->isToday(),
            'timeAgo' => $this->startTime->diffForHumans(),
            'endTimeTs' => $this->endTime?->timestamp,
            'endDate' => $this->endTime?->format('d/m/Y g:i A') ?? null,
            $this->mergeWhen($this->resource->relationLoaded('scores'), function () {
                $default = ['homeScore' => 0, 'awayScore' => 0];
                if (!$this->resource->scores->count()) return $default;
                $score = $this->resource->scores->first(fn($score) => $score->type ==  $this->sport->finalScoreType());
                return ['homeScore' => $score->home ?? 0, 'awayScore' => $score->away ?? 0];
            }),

            'scores' => Score::collection($this->whenLoaded('scores')),
            'league' => new League($this->whenLoaded('league')),
            'homeTeam' => new Team($this->whenLoaded('homeTeam')),
            'awayTeam' => new Team($this->whenLoaded('awayTeam')),
            'winner' => new Team($this->whenLoaded('winner')),
            'markets' => Market::collection($this->whenLoaded('markets')),
            'gameMarkets' => GameMarket::collection($this->whenLoaded('gameMarkets')),
            'stakes' => Stake::collection($this->whenLoaded('stakes')),
            'lays' => StatStake::collection($this->whenLoaded('lays')),
            'backs' => StatStake::collection($this->whenLoaded('backs')),
            'trades' => StatTrade::collection($this->whenLoaded('trades')),
            'last_trade' => new StatTrade($this->whenLoaded('last_trade')),
            'lastTrades' => StatTrade::collection($this->whenLoaded('lastTrades')),
            'tickets' => Ticket::collection($this->whenLoaded('tickets')),
            'wagers' => Wager::collection($this->whenLoaded('wagers')),
            'odds' => Odd::collection($this->whenLoaded('odds')),
            'winBets' => Bet::collection($this->whenLoaded('winBets')),
        ];
    }
}
