<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Market extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'segment' => $this->segment,
            'sport' => $this->sport,
            'oddsId' => $this->oddsId,
            'active' => $this->active,
            'bookie_active' => $this->bookie_active,
            'bets' => Bet::collection($this->whenLoaded('bets')),
            'games' => Game::collection($this->whenLoaded('games')),
        ];
    }
}
