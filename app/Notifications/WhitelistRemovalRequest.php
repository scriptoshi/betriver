<?php

namespace App\Notifications;

use App\Models\Whitelist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WhitelistRemovalRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $whitelist;
    protected $removalToken;

    /**
     * Create a new notification instance.
     *
     * @param Whitelist $whitelist
     * @param string $removalToken
     */
    public function __construct(Whitelist $whitelist, string $removalToken)
    {
        $this->whitelist = $whitelist;
        $this->removalToken = $removalToken;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('whitelists.complete.removal', [
            'whitelist' => $this->whitelist->id,
            'removalToken' => $this->removalToken,
        ]);
        return (new MailMessage)
            ->subject('Confirm Removal of Whitelisted Address')
            ->line('You have requested to remove a whitelisted payout address.')
            ->line("Currency: {$this->whitelist->currency->name}")
            ->line("Address: {$this->whitelist->payout_address}")
            ->action('Confirm Removal', $url)
            ->line('If you did not request this, please ignore this email and take steps to secure your account.');
    }
}
