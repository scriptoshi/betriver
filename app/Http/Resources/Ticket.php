<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Ticket extends JsonResource
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
			'user_id'=>$this->user_id,
			'uid'=>$this->uid,
			'amount'=>$this->amount,
			'payout'=>$this->payout,
			'total_odds'=>$this->total_odds,
			'status'=>$this->status,
			'won'=>$this->won,
			'is_withdrawn'=>$this->is_withdrawn,
			'user'=> new User($this->whenLoaded('user')),
			'wagers'=> Wager::collection($this->whenLoaded('wagers')),
		];
    }
}
