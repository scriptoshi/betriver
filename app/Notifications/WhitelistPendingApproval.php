<?php

namespace App\Notifications;

use App\Models\Whitelist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WhitelistPendingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Whitelist $whitelist, public string $approvalToken) {}

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
        $url = route('whitelists.approve', [
            'whitelist' => $this->whitelist->id,
            'approvalToken' => $this->approvalToken,
        ]);

        return (new MailMessage)
            ->subject('Approve Your Withdrawal Address')
            ->line('A new withdrawal address has been added to your account.')
            ->line('Address: ' . $this->whitelist->payout_address)
            ->line('Currency: ' . $this->whitelist->currency->name)
            ->action('Approve Address', $url)
            ->line('If you did not request this change, no further action is required.');
    }
}
