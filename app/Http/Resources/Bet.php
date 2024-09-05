<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Bet extends JsonResource
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
            'id' => $this->id,
            'uid' => md5($this->id),
            'market_id' => $this->market_id,
            'market_uid' => md5($this->market_id),
            'name' => $this->name,
            'sport' => $this->sport,
            'result' => $this->result,
            'has_odds' => $this->has_odds ?? false,
            'market' => new Market($this->whenLoaded('market')),
            'odds' => Odd::collection($this->whenLoaded('odds')),
            'stakes' => Stake::collection($this->whenLoaded('stakes')),
            'lays' => StatStake::collection($this->whenLoaded('lays')),
            'backs' => StatStake::collection($this->whenLoaded('backs')),
            'trades' => StatTrade::collection($this->whenLoaded('trades')),
            'last_trade' => new StatTrade($this->whenLoaded('last_trade')),
            'tickets' => Ticket::collection($this->whenLoaded('tickets')),
            'wagers' => Wager::collection($this->whenLoaded('wagers')),
            'winGames' => Game::collection($this->whenLoaded('winGames')),
        ];
    }
}
