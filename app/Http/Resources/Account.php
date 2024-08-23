<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Account extends JsonResource
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
            'accountable' => $this->accountable,
            'type' => $this->type,
            'amount' => $this->amount,
            'accountable' => new ($this->whenLoaded('accountable')),
        ];
    }
}
