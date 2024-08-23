<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Payout extends JsonResource
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
            'commission_id' => $this->commission_id,
            'user_id' => $this->user_id,
            'referral_id' => $this->referral_id,
            'uuid' => $this->uuid,
            'description' => $this->description,
            'amount' => $this->amount,
            'percent' => $this->percent,
            'commission' => new Commission($this->whenLoaded('commission')),
            'user' => new User($this->whenLoaded('user')),
            'referral' => new User($this->whenLoaded('referral')),
            'transaction' => new Transaction($this->whenLoaded('transaction')),
        ];
    }
}
