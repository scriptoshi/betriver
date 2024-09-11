<?php

namespace App\Enums;

enum TransactionType: string
{
    case WITHDRAW = 'withdraw';
    case WITHDRAW_CANCELLED = 'withdraw_cancelled';
    case TRANSFER = 'transfer';
    case DEPOSIT = 'deposit';
    case WIN = 'win';
    case BET = 'bet';
    case TICKET = 'ticket';
    case BET_REFUND = 'bet_refund';
    case PAYOUT = 'payout';
    case TRADE_OUT_LIABILITY = 'tradeout_liability';
    case TRADE_OUT_EXPOSURE = 'tradeout_exposure'; // reduction in exposure
    case TRADE_OUT_PROFIT = 'tradeout_profit';
    case REFERRAL_COMMISSION = 'ref_commission';
    case LEVEL_UP = 'level_upgrade';
    case ADMIN_ACTION = 'admin_action';
    case REVERSED = 'reversed';


    /**
     * Get the human-readable name for the transaction type.
     *
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::WITHDRAW => 'Withdraw',
            self::WITHDRAW_CANCELLED => 'Withdraw Cancelled',
            self::TRANSFER => 'Transfer',
            self::DEPOSIT => 'Deposit',
            self::WIN => 'Win',
            self::BET => 'Bet',
            self::TICKET => 'Ticket',
            self::BET_REFUND => 'Bet Refund',
            self::PAYOUT => 'Payout',
            self::TRADE_OUT_LIABILITY => 'Trade Out Liability',
            self::TRADE_OUT_PROFIT => 'Trade Out Profit',
            self::TRADE_OUT_EXPOSURE => 'Exposure Reduced',
            self::REFERRAL_COMMISSION => 'Ref Earnings',
            self::LEVEL_UP => 'Level Up',
            self::ADMIN_ACTION => 'Team Action',
            self::REVERSED => 'Admin Reversed',
        };
    }

    /**
     * Get an array of all transaction type names.
     *
     * @return array<string, array>
     */
    public static function getNames(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = [
                'name' => $case->name(),
                'value' => $case->value
            ];
            return $carry;
        }, []);
    }
}
