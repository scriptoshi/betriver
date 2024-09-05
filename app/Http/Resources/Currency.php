<?php

namespace App\Http\Resources;

use App\Enums\WithdrawGateway;
use Illuminate\Http\Resources\Json\JsonResource;

class Currency extends JsonResource
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
            'gateway' => $this->gateway,
            'gateway' => [
                'gid' => $this->gateway,
                ...WithdrawGateway::from($this->gateway)->info()
            ],
            'code' => $this->code,
            'symbol' => $this->symbol,
            'name' => $this->name,
            'regex' => $this->regex,
            'logo_url' => $this->logo_url,
            'chain' => $this->chain,
            'contract' => $this->contract,
            'explorer' => $this->explorer,
            'rate' => $this->rate,
            'precision' => $this->precision,
            'active' => $this->active,
        ];
    }
}
