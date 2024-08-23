<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Commission extends JsonResource
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
            'type' => $this->type,
            'level' => $this->level,
            'percent' => $this->percent,
            'active' => $this->active,
            'payouts' => Payout::collection($this->whenLoaded('payouts')),
        ];
    }
}
