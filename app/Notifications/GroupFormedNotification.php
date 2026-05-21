<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\NextSmsService;

use Illuminate\Contracts\Queue\ShouldQueue;

class GroupFormedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
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
            ->subject('Group Formation Notification')
            ->greeting('Hello ' . $notifiable->name . '!');

        if ($this->type === 'student') {
            $mail->line('Congratulation You have been selected to join "' . $this->data['group_name'] . '"')
                ->line('And your supervisor is')
                ->line($this->data['supervisor_name'])
                ->line($this->data['supervisor_phone'])
                ->line($this->data['supervisor_email'])
                ->line('Login to your account to view your Team');
        } else {
            $mail->line('Congratulation You have been Assigned to supervising')
                ->line(implode(', ', $this->data['groups']))
                ->line('Login to your account to organize the projects');
        }

        return $mail->action('Visit System', 'http://gptfms.jezdantech.com')
            ->line('Thank you for using our system!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Group Formed',
            'message' => $this->type === 'student' ? 'You have been assigned to ' . $this->data['group_name'] : 'You have been assigned new groups to supervise.',
            'type' => 'group_formation',
            'icon' => 'uil-users-alt'
        ];
    }

    public function sendSms(object $notifiable)
    {
        if ($notifiable->phone) {
            $smsService = new NextSmsService();
            if ($this->type === 'student') {
                $message = "Hello {$notifiable->name}, Congratulation! You joined {$this->data['group_name']}. Supervisor: {$this->data['supervisor_name']} ({$this->data['supervisor_phone']}). Visit: gptfms.jezdantech.com";
            } else {
                $groups = implode(', ', $this->data['groups']);
                $message = "Hello {$notifiable->name}, Congratulation! You are assigned to supervise: {$groups}. Visit: gptfms.jezdantech.com";
            }
            
            $smsService->sendSms($notifiable->phone, $message);
        }
    }
}
