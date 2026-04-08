<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GlobalGenericNotification extends Notification
{
    use Queueable;

    protected $details;

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->details['message'] ?? '',
            'link'    => $this->details['link'] ?? '#',
            'icon'    => $this->details['icon'] ?? 'bx bx-bell',
            'color'   => $this->details['color'] ?? 'primary',
            'title'   => $this->details['title'] ?? 'Notifikasi Baru',
        ];
    }
}
