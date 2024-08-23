<?php



namespace App\Http\Resources;

use App\Support\TradeManager;
use Illuminate\Http\Resources\Json\JsonResource;

class Stake extends JsonResource
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
            'user_id' => $this->user_id,
            'bet_id' => $this->bet_id,
            'game_id' => $this->game_id,
            'uid' => $this->uid,
            'scoreType' => $this->scoreType,
            'amount' => $this->amount,
            'filled' => $this->filled,
            'unfilled' => $this->unfilled,
            'qty' => $this->qty,
            'payout' => $this->payout,
            'liability' => $this->liability,
            'profit_loss' => $this->profit_loss,
            'odds' => $this->odds,
            'american_odds' => TradeManager::decimalToAmericanOdds($this->odds),
            'percentage_odds' => TradeManager::decimalToPercentageOdds($this->odds),
            'price' => $this->price ?? $this->odds, // query
            'type' => $this->type,
            'status' => $this->status,
            'won' => $this->won,
            'is_withdrawn' => $this->is_withdrawn,
            'allow_partial' => $this->allow_partial,
            'is_trade_out' => $this->is_trade_out,
            'is_traded_out' => $this->is_traded_out,
            'user' => new User($this->whenLoaded('user')),
            'game' => new Game($this->whenLoaded('game')),
            'bet' => new Bet($this->whenLoaded('bet')),
            'market' => new Market($this->whenLoaded('market')),
            'maker_trades' => Trade::collection($this->whenLoaded('maker_trades')),
            'taker_trades' => Trade::collection($this->whenLoaded('taker_trades')),
        ];
    }
}
