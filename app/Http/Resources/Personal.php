<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Personal extends JsonResource
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
            'user_id' => $this->user_id,
            'proof_of_identity' => $this->proof_of_identity,
            'proof_of_identity_type' => $this->proof_of_identity_type,
            'proof_of_address' => $this->proof_of_address,
            'proof_of_address_type' => $this->proof_of_address_type,
            'bet_emails' => $this->bet_emails,
            'mailing_list' => $this->mailing_list,
            'confirm_bets' => $this->confirm_bets,
            'daily_gross_deposit' => $this->daily_gross_deposit,
            'weekly_gross_deposit' => $this->weekly_gross_deposit,
            'monthly_gross_deposit' => $this->monthly_gross_deposit,
            'loss_limit_interval' => $this->loss_limit_interval,
            'loss_limit' => $this->loss_limit,
            'stake_limit' => $this->stake_limit,
            'time_out_at' => $this->time_out_at,
            'stake_limit_at' => $this->stake_limit_at,
            'nextStakeLimitAt' => $this->stake_limit_at && now()->subWeek()->lt($this->stake_limit_at) ? $this->stake_limit_at->addWeek()->diffForHumans() : null,
            'canUpdateStake' => $this->stake_limit_at ? now()->subWeek()->gt($this->stake_limit_at) : true,
            'isOnTimeOut' => !!$this->time_out_at,
            'timeOutEndIn' => $this->time_out_at?->diffForHumans() ?? null,
            'dob' => $this->dob,
            'user' => new User($this->whenLoaded('user')),
        ];
    }
}
