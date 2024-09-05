<?php



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
            'id' => $this->id,
            'uid' => md5($this->id),
            'sequence' => $this->sequence,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'segment' => $this->segment,
            'sport' => $this->sport,
            'oddsId' => $this->oddsId,
            'traded' => $this->traded,
            'volume' => $this->volume,
            'liquidity' => $this->liquidity,
            'active' => $this->active,
            'is_default' => $this->active,
            'bookie_active' => $this->bookie_active,
            'has_odds' => $this->has_odds,
            'bets' => Bet::collection($this->whenLoaded('bets')),
            'games' => Game::collection($this->whenLoaded('games')),
            'gameMarket' => $this->whenPivotLoaded('game_market', function () {
                return  new GameMarket($this->pivot);
            }),
            'gameMarkets' => GameMarket::collection($this->whenLoaded('gameMarkets')),
        ];
    }
}
