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
            'phone' => $this->phone,
            'referral' => $this->referral,
            'refId' => $this->refId,
            'balance' => $this->balance ?? 0,
            'hide_balance' => $this->hide_balance ?? 0,
            'odds_type' => $this->odds_type ?? 0,
            'lang' => $this->lang ?? 0,
            'bonus' => $this->bonus,
            'level' => $this->level,
            'l' => $this->level->level(),
            'isLevelOne' => $this->level->level() == 1,
            'isLevelTwo' => $this->level->level() == 2,
            'isLevelThree' => $this->level->level() == 3,
            'levelConfig' => $this->level->config(),
            'profile_photo_path' => $this->profile_photo_path,
            'profile_photo_url' => $this->profile_photo_url,
            'is_admin' => $this->is_admin,
            'requested_next_level' => $this->requested_next_level > 0 ? $this->requested_next_level : null,
            'isBanned' => !is_null($this->banned_at),
            'isKycVerified' => !is_null($this->kyc_verified_at),
            'isPhoneVerified' => !is_null($this->phone_verified_at),
            'isEmailVerified' => !is_null($this->email_verified_at),
            'isAddressVerified' => !is_null($this->address_verified_at),

            'banned_at' => $this->banned_at?->toDateTimeString() ?? null,
            'kyc_verified_at' => $this->kyc_verified_at?->toDateTimeString() ?? null,
            'address_verified_at' => $this->address_verified_at?->toDateTimeString() ?? null,
            'phone_verified_at' => $this->phone_verified_at?->toDateTimeString() ?? null,
            'created_at' => $this->created_at?->toDateTimeString() ?? null,
            'verified_at' => $this->verified_at?->toDateTimeString() ?? null,
            //'slips' => Slip::collection($this->whenLoaded('slips')),
            'stakes' => Stake::collection($this->whenLoaded('stakes')),
            'tickets' => Ticket::collection($this->whenLoaded('tickets')),
            'personal' => new Personal($this->whenLoaded('personal')),
        ];
    }
}
