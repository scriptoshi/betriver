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
            'market_id' => $this->market_id,
            'name' => $this->name,
            'sport' => $this->sport,
            'result' => $this->result,
            'market' => new Market($this->whenLoaded('market')),
            'odds' => Odd::collection($this->whenLoaded('odds')),
            'stakes' => Stake::collection($this->whenLoaded('stakes')),
            'tickets' => Ticket::collection($this->whenLoaded('tickets')),
            'wagers' => Wager::collection($this->whenLoaded('wagers')),
            'winGames' => Game::collection($this->whenLoaded('winGames')),
        ];
    }
}
