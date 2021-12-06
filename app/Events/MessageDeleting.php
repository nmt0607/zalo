<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleting
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private $messageId;

    public function setData($data)
    {
        $this->messageId = $data['message_id'];
    }

    public function getMessageId()
    {
        return $this->messageId;
    }
}
