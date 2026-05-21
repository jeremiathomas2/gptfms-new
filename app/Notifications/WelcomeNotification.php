<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\NextSmsService;

use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $password;
    protected $type;

    public function __construct($password, $type)
    {
        $this->password = $password;
        $this->type = $type; // 'student' or 'supervisor'
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Trigger SMS as well (this will run on the queue)
        $this->sendSms($notifiable);

        $mail = (new MailMessage)
            ->subject('Welcome to GPTF Management System')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Welcome to GPTF Management System');

        if ($this->type === 'student') {
            $mail->line('Kindly Login to your account to complete filling your skills for enhancement of group formation');
        } else {
            $mail->line('Kindly Login to your account to view your groups assigned to supervise');
        }

        return $mail->line('Username : ' . $notifiable->email)
            ->line('Password : ' . $this->password)
            ->line('Update your password after login for security')
            ->action('Visit System', 'http://gptfms.jezdantech.com')
            ->line('Thank you for using our system!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Welcome to GPTFMS',
            'message' => 'Your account has been created successfully.',
            'type' => 'welcome',
            'icon' => 'uil-user-check'
        ];
    }

    public function sendSms(object $notifiable)
    {
        if ($notifiable->phone) {
            $smsService = new NextSmsService();
            $message = "Hello {$notifiable->name} Welcome to GPTF Management System. ";
            if ($this->type === 'student') {
                $message .= "Kindly Login to complete filling your skills. ";
            } else {
                $message .= "Kindly Login to view your assigned groups. ";
            }
            $message .= "User: {$notifiable->email}, Pwd: {$this->password}. Link: gptfms.jezdantech.com";
            
            $smsService->sendSms($notifiable->phone, $message);
        }
    }
}
