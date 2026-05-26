<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\SystemSetting;

class PasswordResetOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $otp,
        private readonly int $minutesValid
    ) {
    }

    public function via(object $notifiable): array
    {
        return SystemSetting::getBool('notify.email_enabled', true) ? ['mail'] : [];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Your Password Reset OTP')
            ->greeting('Hello ' . ($notifiable->name ?? 'User') . '!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Use the OTP below to proceed with the reset:')
            ->line($this->otp)
            ->line("This OTP is valid for {$this->minutesValid} minutes.")
            ->line('If you did not request this, no further action is required.');
    }
}
