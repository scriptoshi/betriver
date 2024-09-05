<?php

namespace App\Http\Resources;

use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Models\Stake as StakeModel;
use App\Models\Ticket as TicketModel;
use App\Models\Withdraw as WithdrawModel;
use App\Models\Deposit as DepositModel;
use App\Models\Payout as PayoutModel;
use App\Traits\WhenMorphed;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class Transaction extends JsonResource
{

    use WhenMorphed;
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
            'transactable' => $this->transactable,
            'uuid' => $this->uuid,
            'uid' => Str::before($this->uuid, '-'),
            'description' => $this->description,
            'amount' => $this->amount,
            'balance_before' => $this->balance_before,
            'action' => $this->action,
            'type' => $this->type,
            // derived
            'isCredit' => $this->action == TransactionAction::CREDIT,
            'isDebit' => $this->action == TransactionAction::DEBIT,
            'isWithdraw' => get_class($this->transactable) ==  WithdrawModel::class,
            'isDeposit' =>  get_class($this->transactable) ==  DepositModel::class,
            'isStake' => get_class($this->transactable) ==  StakeModel::class,
            'isTicket' => get_class($this->transactable) ==  TicketModel::class,
            'isPayout' => get_class($this->transactable) ==  PayoutModel::class,
            'isLevel' => $this->type == TransactionType::LEVEL_UP,
            'created_at' => $this->created_at->toDateString(),
            'user' => new User($this->whenLoaded('user')),
            'transactable' => $this->whenMorphed('transactable'),
        ];
    }
}
