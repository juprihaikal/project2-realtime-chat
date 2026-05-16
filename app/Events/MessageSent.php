<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
   use Dispatchable, SerializesModels;

    public $message;
    public $receiverId;
    public function __construct(
        Message $message,
        $receiverId
    )
    {
        $this->message =
        $message->load('user');

        $this->receiverId =
        $receiverId;
    }

    public function broadcastOn(): array
    {
        return [

            new PrivateChannel(
                'chat.' . $this->receiverId
            )

        ];
    }
}
