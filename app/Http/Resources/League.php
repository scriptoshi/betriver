<?php



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
            'slug' => $this->slug,
            'sport' => $this->sport,
            'race_tag' => $this->race_tag,
            'race_tag_name' => $this->race_tag?->name(),
            'season' => $this->season,
            'season_ends_at' => $this->season_ends_at,
            'season_ends' => $this->season_ends_at?->toFormattedDateString(),
            'country' => $this->country,
            'description' => $this->description,
            'active' => $this->active,
            'menu' => $this->menu,
            'has_odds' => $this->has_odds,
            'image' => $this->image,
            'games' => Game::collection($this->whenLoaded('games')),
        ];
    }
}
