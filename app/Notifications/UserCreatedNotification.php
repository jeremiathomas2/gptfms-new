<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SystemSetting;
use App\Notifications\Channels\SmsChannel;

use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        $channels = [];
        if (SystemSetting::getBool('notify.email_enabled', true)) {
            $channels[] = 'mail';
        }
        if (SystemSetting::getBool('notify.sms_enabled', true) && !empty($notifiable->phone)) {
            $channels[] = SmsChannel::class;
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $role = $notifiable->hasRole('supervisor') ? 'Supervisor' : 'Student';
        return (new MailMessage)
            ->subject('Welcome to GPTFMS')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have been registered as a ' . $role . ' in the Final Year Management System (GPTFMS).')
            ->line('Your login credentials are:')
            ->line('Email: ' . $notifiable->email)
            ->line('Password: ' . $this->password)
            ->action('Login Now', url('/login'))
            ->line('Please change your password after logging in for the first time.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account Created',
            'message' => 'Your account has been created as a ' . ($notifiable->hasRole('supervisor') ? 'Supervisor' : 'Student'),
            'type' => 'account_creation',
            'icon' => 'uil-user-check'
        ];
    }

    public function toSms(object $notifiable): string
    {
        return "Hello {$notifiable->name}, your GPTFMS account is ready. Email: {$notifiable->email}, Pwd: {$this->password}. Login at: " . url('/login');
    }
}
