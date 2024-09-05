<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Feedback extends JsonResource
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
			'feedback'=>$this->feedback,
			'user'=> new User($this->whenLoaded('user')),
		];
    }
}
