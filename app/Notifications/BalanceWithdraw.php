<?php

namespace App\Notifications;

use App\Gateways\Sms\SmsChannel;
use App\Models\Withdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BalanceWithdraw extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Withdraw $tx)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (settings('sms_gateway', null))
            return ['mail', SmsChannel::class];
        return ['main'];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $vars = [
            'amount' => $this->tx->amount,
            'status' => $this->tx->status->value,
            'symbol' => settings('site.currency_code'),
            'balance' => $notifiable->balance,
            ...(array)$notifiable,
            'appname' => config('app.name')
        ];
        $message = settings('mail.balance_withdraw_message', 'Your withdraw request for :amount :symbol has been processed in your account. Your new balance is :balance :symbol');
        $subject = settings('mail.balance_withdraw_subject', '[:appname] :symbol Withdraw Processed ');
        $salutation = settings('mail.balance_withdraw_salutation', ':symbol withdraw :status');
        return (new MailMessage)
            ->subject(__($subject, $vars))
            ->salutation(__($salutation, $vars))
            ->line(__($message, $vars))
            ->line('WITHDRAW STATUS ' . strtoupper($this->tx->status->value))
            ->action(__('View Your statement'), url('/account/statement'))
            ->line(__('Regards'))
            ->line(__('This is an automated message, please do not reply.'));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $vars = [
            'amount' => $this->tx->amount,
            'status' => $this->tx->status->value,
            'symbol' => settings('site.currency_code'),
            'balance' => $notifiable->balance,
            ...(array)$notifiable,
            'appname' => config('app.name')
        ];
        $message = settings('sms.balance_withdraw_message', 'withdraw :amount :symbol processed. status :status');
        return __($message, $vars);
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
