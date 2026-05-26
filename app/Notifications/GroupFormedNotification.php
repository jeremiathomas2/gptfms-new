<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SystemSetting;
use App\Notifications\Channels\SmsChannel;

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

    public function toSms(object $notifiable): string
    {
        if ($this->type === 'student') {
            return "Hello {$notifiable->name}, Congratulation! You joined {$this->data['group_name']}. Supervisor: {$this->data['supervisor_name']} ({$this->data['supervisor_phone']}). Visit: gptfms.jezdantech.com";
        }
        $groups = implode(', ', $this->data['groups']);
        return "Hello {$notifiable->name}, Congratulation! You are assigned to supervise: {$groups}. Visit: gptfms.jezdantech.com";
    }
}
