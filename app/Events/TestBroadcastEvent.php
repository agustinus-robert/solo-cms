<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestBroadcastEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Kita pakai Public Channel dulu agar tidak perlu urusan login/auth
        return new Channel('test-channel');
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }

    public function broadcastAs()
    {
        return 'test.event';
    }
}
