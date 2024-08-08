<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'uuid' => $this->uuid,
            'id' => $this->id,
            'league_id' => $this->league_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'name' => $this->name,
            'startTime' => $this->startTime,
            'startTimeAgo' =>  $this->startTime->diffForHumans(),
            'endTime' => $this->endTime,
            'status' => $this->status,
            'statusText' => $this->status->statusText(),
            'sport' => $this->sport,
            'active' => $this->active,
            'scores' => Score::collection($this->whenLoaded('scores')),
            'league' => new League($this->whenLoaded('league')),
            'homeTeam' => new Team($this->whenLoaded('homeTeam')),
            'awayTeam' => new Team($this->whenLoaded('awayTeam')),
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
