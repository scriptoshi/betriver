<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Deposit extends JsonResource
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
            'from' => $this->from,
            'gross_amount' => $this->gross_amount,
            'fees' => $this->fees,
            'amount' => $this->amount,
            'data' => $this->data,
            'status' => $this->status,
            'deposit_address' => $this->deposit_address,
            'amount_currency' => $this->amount_currency,
            'gateway_currency' => $this->gateway_currency,
            'gateway_amount' => $this->gateway_amount,
            'user' => new User($this->whenLoaded('user')),
            'transaction' => new Transaction($this->whenLoaded('transaction')),
        ];
    }
}
