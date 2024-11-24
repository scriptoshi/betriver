<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameMarket extends JsonResource
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
            'uuid' => "gamemarket.$this->id", // channel for websockets
            'active' => $this->active,
            'bookie_active' => $this->bookie_active,
            'winning_bet_id' => $this->winning_bet_id,
            'market_id' => $this->market_id,
            'game_id' => $this->game_id,
            'game' => new Game($this->whenLoaded('game')),
            'market' => new Market($this->whenLoaded('market')),
            'winningBet' => new Market($this->whenLoaded('winningBet')),
        ];
    }
}
