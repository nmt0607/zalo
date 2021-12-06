<?php

namespace App\Providers;

use App\Events\AuthenticatingWithToken;
use App\Events\ConversationJoining;
use App\Events\MessageSending;
use App\Listeners\AuthenticateToken;
use App\Listeners\CreateMessage;
use App\Listeners\JoinConversation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        AuthenticatingWithToken::class => [
            AuthenticateToken::class,
        ],
        ConversationJoining::class => [
            JoinConversation::class,
        ],
        MessageSending::class => [
            CreateMessage::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
