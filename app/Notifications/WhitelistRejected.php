<?php

namespace App\Notifications;

use App\Models\Whitelist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WhitelistRejected extends Notification implements ShouldQueue
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
            ->subject('Withdrawal Address Rejected')
            ->line('Your withdrawal address has been rejected by support team.')
            ->line('If you think this was done in error, contact our support team for help.')
            ->line('Address: ' . $this->whitelist->payout_address)
            ->line('Currency: ' . $this->whitelist->currency->name)
            ->action('View Your Whitelists', url('/whitelists'))
            ->line('Thank you for using our application!');
    }
}
