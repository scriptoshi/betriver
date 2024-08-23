<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->email,
            'referral' => $this->referral,
            'balance' => $this->balance ?? 0,
            'hide_balance' => $this->hide_balance ?? 0,
            'odds_type' => $this->odds_type ?? 0,
            'lang' => $this->lang ?? 0,
            'bonus' => $this->bonus,
            'profile_photo_path' => $this->profile_photo_path,
            'profile_photo_url' => $this->profile_photo_url,
            'is_admin' => $this->is_admin,
            'isBanned' => !is_null($this->banned_at),
            'isKycVerified' => !is_null($this->kyc_verified_at),
            'banned_at' => $this->created_at->toDateTimeString(),
            'kyc_verified_at' => $this->created_at->toDateTimeString(),
            'phone_verified_at' => $this->created_at->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'verified_at' => $this->created_at->toDateTimeString(),
            //'slips' => Slip::collection($this->whenLoaded('slips')),
            'stakes' => Stake::collection($this->whenLoaded('stakes')),
            'tickets' => Ticket::collection($this->whenLoaded('tickets')),
        ];
    }
}
