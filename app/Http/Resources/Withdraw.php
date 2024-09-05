<?php



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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'uuid' => $this->uuid,
            'uid' => str($this->uuid)->before('-'),
            'gateway' => [
                'gid' => $this->gateway,
                ...$this->gateway->info()
            ],
            'remoteId' => $this->remoteId,
            'batchId' => $this->batchId,
            'to' => $this->to,
            'gross_amount' => $this->gross_amount,
            'gateway_amount' => $this->gateway_amount,
            'fees' => $this->fees,
            'amount' => $this->amount,
            'data' => $this->data,
            'status' => $this->status,
            'gateway_error' => $this->gateway_error,
            'batch_id' => $this->batch_id,
            'currency' => $this->currency,
            'gateway_currency' => $this->gateway_currency,
            'created_at' => $this->created_at,
            'crypto_amount' => $this->crypto_amount,
            'user' => new User($this->whenLoaded('user')),
            'transaction' => new Transaction($this->whenLoaded('transaction')),
        ];
    }
}
