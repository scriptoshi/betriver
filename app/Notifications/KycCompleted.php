<?php

namespace App\Notifications;

use App\Gateways\Sms\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycCompleted extends Notification implements ShouldQueue
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
        $message = settings('mail.kyc_completed_message', 'Congratulations, your KYC verification is complete and any account limits have been removed.');
        $subject = settings('mail.kyc_completed_subject', 'KYC Verification Complete');
        $salutation = settings('mail.kyc_completed_salutation', 'You are now verified');
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
        $message = settings('sms.kyc_completed_message', 'Congratulations, your KYC verification is complete');
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
