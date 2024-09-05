<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Whitelist extends JsonResource
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
            'user_id' => $this->user_id,
            'uuid' => $this->uuid,
            'uid' => str($this->uuid)->before('-'),
            'currency_id' => $this->currency_id,
            'payout_address' => $this->payout_address,
            'approved' => $this->approved,
            'status' => $this->status,
            'user' => new User($this->whenLoaded('user')),
            'currency' => new Currency($this->whenLoaded('currency')),
        ];
    }
}
