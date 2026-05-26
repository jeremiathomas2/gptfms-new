<?php

namespace App\Notifications\Channels;

use App\Services\NextSmsService;

class SmsChannel
{
    public function send(object $notifiable, object $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $phone = $notifiable->phone ?? null;
        if (!$phone) {
            return;
        }

        $message = $notification->toSms($notifiable);
        if (!is_string($message) || trim($message) === '') {
            return;
        }

        $service = new NextSmsService();
        $service->sendSms($phone, $message);
    }
}

