<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Team extends JsonResource
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
            'teamId' => $this->teamId,
            'name' => $this->name,
            'code' => $this->code,
            'country' => $this->country,
            'description' => $this->description,
            'sport' => $this->sport,
            'active' => $this->active,
            'image' => $this->image,
            'home_games' => Game::collection($this->whenLoaded('home_games')),
            'away_games' => Game::collection($this->whenLoaded('away_games')),
        ];
    }
}
