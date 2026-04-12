<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class GlobalGenericNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $details;

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
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

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->details['message'] ?? '',
            'title'   => $this->details['title'] ?? 'Notifikasi Baru',
            'link'    => $this->details['link'] ?? '#',
            'icon'    => $this->details['icon'] ?? 'bx bx-bell',
            'color'   => $this->details['color'] ?? 'primary',
        ]);
    }
}
