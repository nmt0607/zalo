<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuthenticatingWithToken
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private $token;

    public function setData(array $data)
    {
        $this->token = $data['token'];

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }
}
