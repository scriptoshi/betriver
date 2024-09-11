<?php



namespace App\Http\Resources;

use App\Support\TradeManager;
use Illuminate\Http\Resources\Json\JsonResource;

class Odd extends JsonResource
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
            'bet_id' => $this->bet_id,
            'game_id' => $this->game_id,
            'market_id' => $this->market_id,
            'oddId' => $this->oddId,
            'md5' => $this->md5,
            'odd' => $this->odd,
            'american_odd' => TradeManager::decimalToAmericanOdds($this->odd),
            'percentage_odd' => TradeManager::decimalToPercentageOdds($this->odd),
            'active' => $this->active,
            'bet' => new Bet($this->whenLoaded('bet')),
            'game' => new Game($this->whenLoaded('game')),
            'market' => new Market($this->whenLoaded('market')),
        ];
    }
}
