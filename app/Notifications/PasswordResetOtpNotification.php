<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $otp,
        private readonly int $minutesValid
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Your Password Reset OTP')
            ->greeting('Hello!')
            ->line('Use the OTP below to reset your password:')
            ->line($this->otp)
            ->line("This OTP expires in {$this->minutesValid} minutes.")
            ->line('If you did not request this, you can ignore this email.');
    }
}

