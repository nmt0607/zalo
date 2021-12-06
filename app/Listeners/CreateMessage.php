<?php

namespace App\Listeners;

use App\Events\MessageSending;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Redis;

class CreateMessage
{
    /** @var MessageService */
    protected $messageService;

    /** @var ConversationService */
    protected $conversationService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        MessageService $messageService,
        ConversationService $conversationService
    ) {
        $this->messageService = $messageService;
        $this->conversationService = $conversationService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MessageSending $event)
    {
        [
            'sender' => $sender,
            'receiver' => $receiver,
            'content' => $content,
        ] = $event->getData();
        $conversation = $this
            ->conversationService
            ->findConversationByUserIds([$sender['id'], $receiver['id']]);
        $message = $this->messageService->create([
            'from_id' => $sender['id'],
            'to_id' => $receiver['id'],
            'message' => $content,
            'conversation_id' => $conversation->id,
            'is_read' => false,
        ]);

        Redis::publish('chat', json_encode([
            'event' => config('define.events.message_created'),
            'data' => [
                'conversation_id' => $message->conversation->id,
                'sender' => $sender,
                'receiver' => $receiver,
                'message_id' => $message->id,
                'message' => $message->message,
                'created' => $message->created_at,
            ],
        ]));
    }
}
