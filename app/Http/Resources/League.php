<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class League extends JsonResource
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
            'leagueId' => $this->leagueId,
            'name' => $this->name,
            'sport' => $this->sport,
            'season' => $this->season,
            'country' => $this->country,
            'description' => $this->description,
            'active' => $this->active,
            'image' => $this->image,
            'games' => Game::collection($this->whenLoaded('games')),
        ];
    }
}
