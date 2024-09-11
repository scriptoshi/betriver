<?php



namespace App\Http\Resources;

use App\Support\TradeManager;
use Illuminate\Http\Resources\Json\JsonResource;

class Trade extends JsonResource
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
            'maker_id' => $this->maker_id,
            'taker_id' => $this->taker_id,
            'amount' => $this->amount,
            'price' => $this->price,
            'status' => $this->status,
            'commission' => $this->commission,
            'american_price' => TradeManager::decimalToAmericanOdds($this->price),
            'percentage_price' => TradeManager::decimalToPercentageOdds($this->price),
            'layer_price' => $this->price,
            $this->mergeWhen($request->user()->isAdmin(), [
                'buy' => $this->buy,
                'sell' => $this->sell,
                'margin' => $this->margin,
            ]),
            'maker' => new Stake($this->whenLoaded('maker')),
            'taker' => new Stake($this->whenLoaded('taker')),
            'bet' => new Bet($this->whenLoaded('bet')),
            'market' => new Market($this->whenLoaded('market')),
            'game' => new Game($this->whenLoaded('market')),
        ];
    }
}
