<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender', 'receiver');
    }

    public function broadcastOn()
{
    $senderId   = $this->message->sender_id;
    $receiverId = $this->message->receiver_id;

    $channelName = 'chat.' . min($senderId, $receiverId) . '.' . max($senderId, $receiverId);

    return new PrivateChannel($channelName);
}


    public function broadcastAs()
    {
        return 'message.sent';
    }
}

