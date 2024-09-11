<?php



namespace App\Http\Resources;

use App\Support\TradeManager;
use Illuminate\Http\Resources\Json\JsonResource;

class Wager extends JsonResource
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
            'id' => md5($this->id),
            'ticket_id' => $this->ticket_id,
            'bet_id' => $this->bet_id,
            'game_id' => $this->game_id,
            'odd_id' => $this->odd_id,
            'scoreType' => $this->scoreType,
            'odds' => $this->odds,
            'game_info' => $this->game_info,
            'bet_info' => $this->bet_info,
            'market_info' => $this->market_info,
            'american_odds' => TradeManager::decimalToAmericanOdds($this->odds),
            'percentage_odds' => TradeManager::decimalToPercentageOdds($this->odds),
            'won' => $this->won,
            'sport' => $this->sport,
            'status' => $this->status,
            'bet' => new Bet($this->whenLoaded('bet')),
            'ticket' => new Ticket($this->whenLoaded('ticket')),
            'game' => new Game($this->whenLoaded('game')),
            'odd' => new Odd($this->whenLoaded('odd')),
        ];
    }
}
