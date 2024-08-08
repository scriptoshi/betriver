<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Transaction extends JsonResource
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
			'transactable'=>$this->transactable,
			'uuid'=>$this->uuid,
			'description'=>$this->description,
			'amount'=>$this->amount,
			'balance_before'=>$this->balance_before,
			'action'=>$this->action,
			'type'=>$this->type,
			'user'=> new User($this->whenLoaded('user')),
			'transactable'=> new ($this->whenLoaded('transactable')),
		];
    }
}
