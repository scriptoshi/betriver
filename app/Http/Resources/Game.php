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
            'slug' => $this->id . '/' . Str::slug($this->name),
            'league_id' => $this->league_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'win_team_id' => $this->win_team_id,
            'name' => $this->name,
            'startTime' => $this->startTime,
            'startTimeAgo' =>  $this->startTime->diffForHumans(),
            'endTime' => $this->endTime,
            'status' => $this->status,
            'statusText' => $status->statusText(),
            'statusTooltip' => $status->description(),
            'state' => $status->gameState(),
            'stateCancelled' => $status->cancelled(),
            'stateFinished' => $status->finished(),
            'stateEnded' => $status->ended(),
            'finalScoreType' => $this->sport->finalScoreType(),
            'result' => $this->result,
            'rounds' => $this->rounds,
            'elapsed' => $this->elapsed,
            'sport' => $this->sport,
            'active' => $this->active,
            'has_odds' => $this->has_odds,
            'has_scores' => $this->has_scores || ($this->sport === LeagueSport::MMA && !is_null($this->result)),
            'closed' => $this->closed,
            /**
             * Query derived values
             * 
             */

            /**
             *  derived values
             */
            'startTimeFormatted' => $this->startTime->toDateTimeString(),
            'startTimeTs' => $this->startTime->timestamp,
            'startsAt' => $this->startTime->format('H:i'),
            'isSoon' => $this->startTime->greaterThan(now()) && $this->startTime->lessThanOrEqualTo(now()->addHour()),
            'hasStarted' => now()->gt($this->startTime),
            'endTimeFormatted' => $this->endTime ? $this->endTime->toDateTimeString() : null,
            'hasEnded' =>  $this->closed || (!is_null($this->endTime) && now()->gt($this->endTime)),
            'isToday' => $this->startTime->isToday(),
            'timeAgo' => $this->startTime->diffForHumans(),
            'endTimeTs' => $this->endTime?->timestamp,

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
            'stakes' => Stake::collection($this->whenLoaded('stakes')),
            'trades' => Trade::collection($this->whenLoaded('trades')),
            'tickets' => Ticket::collection($this->whenLoaded('tickets')),
            'wagers' => Wager::collection($this->whenLoaded('wagers')),
            'odds' => Odd::collection($this->whenLoaded('odds')),
            'winBets' => Bet::collection($this->whenLoaded('winBets')),
        ];
    }
}
