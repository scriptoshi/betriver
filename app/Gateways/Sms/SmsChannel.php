<?php

namespace App\Gateways\Sms;

use Illuminate\Notifications\Notification;

class SmsChannel
{

    public function __construct()
    {
    }

    public static function client(): Gateway
    {
        $gateway = settings('sms_gateway', 'vonage');
        return match ($gateway) {
            'smspoh' => app(SmspohApi::class),
            'touchsms' => app(TouchSms::class),
            'clickatell' =>  app(Clickatell::class),
            'messagebird' => app(MessageBird::class),
            'vonage' =>  app(Vonage::class),
            'textmagic' =>  app(TextMagic::class),
        };
    }

    public function send($notifiable, Notification $notification): void
    {
        $to = $notifiable->routeNotificationFor('sms') ?? $notifiable->routeNotificationFor(SmsChannel::class);
        if (!$to) return;
        $message = $notification->toSms($notifiable);
        static::client()->send($to, $message);
    }
}
