<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Score extends JsonResource
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
			'game_id'=>$this->game_id,
			'type'=>$this->type,
			'home'=>$this->home,
			'away'=>$this->away,
			'game'=> new Game($this->whenLoaded('game')),
		];
    }
}
