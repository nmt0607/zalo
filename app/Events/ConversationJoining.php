<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationJoining
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private $userIds;
    private $socketId;

    public function setData(array $data)
    {
        $senderId = $data['sender']['id'];
        $receiverId = $data['receiver']['id'];
        $this->userIds = [$senderId, $receiverId];
        $this->socketId = $data['socketId'];

        return $this;
    }

    public function getUserIds()
    {
        return $this->userIds;
    }

    public function getSocketId()
    {
        return $this->socketId;
    }
}
