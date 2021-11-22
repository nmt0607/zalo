<?php

namespace App\Listeners;

use App\Events\AuthenticatingWithToken;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateToken
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AuthenticatingWithToken $event)
    {
        $token = $event->getToken();
        $accessToken = PersonalAccessToken::findToken($token);
        $user = $accessToken?->tokenable;

        Redis::publish("authentication:$token", json_encode([
            'data' => $user,
        ]));
    }
}
