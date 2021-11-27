<?php

namespace App\Listeners;

use App\Events\ConversationJoining;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Redis;

class JoinConversation
{
    /**
     * @var ConversationService
     */
    protected $conversationService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->conversationService = app(ConversationService::class);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ConversationJoining $event)
    {
        $userIds = $event->getUserIds();
        $conversation = $this
            ->conversationService
            ->findConversationByUserIds($userIds);
        if ($conversation === null) {
            $conversation = $this->conversationService->createConversation($userIds);
        }

        Redis::publish('chat', json_encode([
            'event' => config('define.events.conversation_joined'),
            'data' => [
                'conversationId' => $conversation->id,
                'socketId' => $event->getSocketId(),
            ],
        ]));
    }
}
