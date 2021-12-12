<?php

namespace App\Listeners;

use App\Events\MessageSending;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ConversationService;
use App\Services\MessageService;
use App\Services\UserService;
use Illuminate\Support\Facades\Redis;

class CreateMessage
{
    /** @var MessageService */
    protected $messageService;

    /** @var ConversationService */
    protected $conversationService;

    /** @var UserService */
    protected $userService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->messageService = app(MessageService::class);
        $this->conversationService = app(ConversationService::class);
        $this->userService = app(UserService::class);
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
            'sender' => ['id' => $senderId],
            'receiver' => ['id' => $receiverId],
            'content' => $content,
        ] = $event->getData();
        $conversation = $this
            ->conversationService
            ->findConversationByUserIds([$senderId, $receiverId]);
        $sender = User::findOrFail($senderId);
        $receiver = User::findOrFail($receiverId);
        $message = $this->messageService->create([
            'from_id' => $senderId,
            'to_id' => $receiverId,
            'message' => $content,
            'conversation_id' => $conversation->id,
            'is_read' => false,
        ]);

        Redis::publish('chat', json_encode([
            'event' => config('define.events.message_created'),
            'data' => [
                'conversation_id' => $message->conversation->id,
                'sender' => new UserResource($sender),
                'receiver' => new UserResource($receiver),
                'message_id' => $message->id,
                'message' => $message->message,
                'created' => $message->created_at,
            ],
        ]));
    }
}
