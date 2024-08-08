<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Withdraw extends JsonResource
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
            'user_id' => $this->user_id,
            'uuid' => $this->uuid,
            'gateway' => $this->gateway,
            'remoteId' => $this->remoteId,
            'to' => $this->to,
            'gross_amount' => $this->gross_amount,
            'fees' => $this->fees,
            'amount' => $this->amount,
            'data' => $this->data,
            'status' => $this->status,
            'gateway_error' => $this->gateway_error,
            'batch_id' => $this->batch_id,
            'fiat_currency' => $this->fiat_currency,
            'crypto_currency' => $this->crypto_currency,
            'crypto_amount' => $this->crypto_amount,
            'user' => new User($this->whenLoaded('user')),
            'transaction' => new Transaction($this->whenLoaded('transaction')),
        ];
    }
}
