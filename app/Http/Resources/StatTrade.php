<?php



namespace App\Http\Resources;

use App\Support\TradeManager;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StatTrade extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $price = $this->price ?? $this->avg_price ?? $this->odds;
        return [
            'bet_id' => $this->bet_id,
            'amount' => $this->amount ?? $this->total_amount,
            'price' => $price,
            'date' => $this->range_start ? Carbon::parse($this->range_start) : null,
            'american_price' => $price ? TradeManager::decimalToAmericanOdds($price) : null,
            'percentage_price' => $price ? TradeManager::decimalToPercentageOdds($price) : null,

        ];
    }
}
