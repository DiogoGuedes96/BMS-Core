<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationPushed implements ShouldBroadcast
{
    protected $message;
    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }

    public function broadcastOn()
    {
        return new Channel('NotificationChannel');
    }

    // public function broadcastAs()
    // {
    //     return 'send-notification';
    // }
}
