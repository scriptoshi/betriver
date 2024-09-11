<?php



namespace App\Http\Resources;

use App\Support\TradeManager;
use Illuminate\Http\Resources\Json\JsonResource;

class StatStake extends JsonResource
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
            'bet_id' => $this->bet_id ?? null,
            'user_id' => $this->user_id ?? null,
            'amount' => $this->amount,
            'odds' => $this->odds ?? $this->price,
            'american_odds' =>  $this->odds || $this->price ? TradeManager::decimalToAmericanOdds($this->odds ?? $this->price ?? 0) : 0,
            'percentage_odds' => $this->odds || $this->price ? TradeManager::decimalToPercentageOdds($this->odds ?? $this->price ?? 0) : 0,
            'price' => $this->price ?? $this->odds, // query
        ];
    }
}
