<?php

namespace App\Notifications;

use App\Models\Whitelist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WhitelistApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Whitelist $whitelist) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Withdrawal Address Approved')
            ->line('Your withdrawal address has been successfully approved.')
            ->line('Address: ' . $this->whitelist->payout_address)
            ->line('Currency: ' . $this->whitelist->currency->name)
            ->action('View Your Whitelists', url('/whitelists'))
            ->line('Thank you for using our application!');
    }
}
