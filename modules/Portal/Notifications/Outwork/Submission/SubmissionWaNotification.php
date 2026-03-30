<?php

namespace Modules\Portal\Notifications\Outwork\Submission;

use App\Channels\WhatsAppChannel;
use App\Notifications\WhatsAppNotification;

class SubmissionWaNotification extends WhatsAppNotification
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
