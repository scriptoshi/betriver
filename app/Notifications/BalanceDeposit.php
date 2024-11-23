<?php

namespace App\Notifications;

use App\Gateways\Sms\SmsChannel;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BalanceDeposit extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Transaction $tx)
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
            'symbol' => settings('site.currency_code'),
            'balance' => $notifiable->balance,
            ...(array)$notifiable,
            'appname' => config('app.name')
        ];
        $message = settings('mail.balance_deposit_message', ':amount :symbol has been credited to your account. Your new balance is :balance :symbol');
        $subject = settings('mail.balance_deposit_subject', '[:appname] :symbol Deposit Confirmed ');
        $salutation = settings('mail.balance_deposit_salutation', ':symbol Deposit Successful');
        return (new MailMessage)
            ->subject(__($subject, $vars))
            ->salutation(__($salutation, $vars))
            ->line(__($message, $vars))
            ->action(__('View Your Balance'), url('/account/statement'))
            ->line(__('Regards'))
            ->line(__('This is an automated message, please do not reply.'));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSMS(object $notifiable): string
    {
        $message = settings('sms.balance_deposit_message', ':amount :symbol has been credited to your account.');
        return __($message, [
            'amount' => $this->tx->amount,
            'symbol' => settings('site.currency_code'),
            'balance' => $notifiable->balance,
            ...(array)$notifiable,
            'appname' => config('app.name')
        ]);
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
