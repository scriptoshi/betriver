<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Stake extends JsonResource
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
            'slip_id' => $this->slip_id,
            'user_id' => $this->user_id,
            'bet_id' => $this->bet_id,
            'game_id' => $this->game_id,
            'uid' => $this->uid,
            'scoreType' => $this->scoreType,
            'amount' => $this->amount,
            'filled' => $this->filled,
            'unfilled' => $this->unfilled,
            'payout' => $this->payout,
            'odds' => $this->odds,
            'status' => $this->status,
            'won' => $this->won,
            'is_withdrawn' => $this->is_withdrawn,
            'allow_partial' => $this->allow_partial,
            'user' => new User($this->whenLoaded('user')),
            'game' => new Game($this->whenLoaded('game')),
            'bet' => new Bet($this->whenLoaded('bet')),
            'maker_trades' => Trade::collection($this->whenLoaded('maker_trades')),
            'taker_trades' => Trade::collection($this->whenLoaded('taker_trades')),
        ];
    }
}
