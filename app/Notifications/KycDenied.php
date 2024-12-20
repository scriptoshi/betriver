<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Gateways\Sms\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycDenied extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
        $message = settings('mail.kyc_denied_message', 'To ensure you have continued access to deposits, withdrawals, and bets, please resubmit the KYC verification process as soon as possible.');
        $subject = settings('mail.kyc_denied_subject', 'KYC Verification Failed');
        $salutation = settings('mail.kyc_denied_salutation', 'We could not verify your Credentials');
        return (new MailMessage)
            ->subject(__($subject, [...(array)$notifiable, 'appname' => config('app.name')]))
            ->salutation(__($salutation, [...(array)$notifiable, 'appname' => config('app.name')]))
            ->line(__($message, [...(array)$notifiable, 'appname' => config('app.name')]))
            ->action(__('View Your Dashboard'), url('/'))
            ->line(__('Regards'))
            ->line(__('This is an automated message, please do not reply.'));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $message = settings('sms.kyc_denied_message', 'Kyc submission rejected, resubmit as soon as possible.');
        return __($message, [...(array)$notifiable, 'appname' => config('app.name')]);
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
