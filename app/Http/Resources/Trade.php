<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */
namespace App\Http\Resources;

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
			'maker_id'=>$this->maker_id,
			'taker_id'=>$this->taker_id,
			'amount'=>$this->amount,
			'buy'=>$this->buy,
			'sell'=>$this->sell,
			'margin'=>$this->margin,
			'maker'=> new Stake($this->whenLoaded('maker')),
			'taker'=> new Taker($this->whenLoaded('taker')),
		];
    }
}
