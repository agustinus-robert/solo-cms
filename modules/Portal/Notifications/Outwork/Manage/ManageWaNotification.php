<?php

namespace Modules\Portal\Notifications\Outwork\Manage;

use App\Channels\WhatsAppChannel;
use App\Notifications\WhatsAppNotification;

class ManageWaNotification extends WhatsAppNotification
{
    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        return [
            'phone'   => $this->phone,
            'message' => $this->buildMessage(),
            'file'    => $this->file,
        ];
    }
}
