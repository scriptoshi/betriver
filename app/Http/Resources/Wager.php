<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Wager extends JsonResource
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
			'ticket_id'=>$this->ticket_id,
			'bet_id'=>$this->bet_id,
			'game_id'=>$this->game_id,
			'odd_id'=>$this->odd_id,
			'scoreType'=>$this->scoreType,
			'odds'=>$this->odds,
			'winner'=>$this->winner,
			'bet'=> new Bet($this->whenLoaded('bet')),
			'ticket'=> new Ticket($this->whenLoaded('ticket')),
			'game'=> new Game($this->whenLoaded('game')),
			'odd'=> new Odd($this->whenLoaded('odd')),
		];
    }
}
