<?php

namespace App\Listeners;

use App\Events\MessageSending;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ConversationService;
use App\Services\MessageService;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\DB;
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
        $sender = User::findOrFail($senderId);
        $receiver = User::findOrFail($receiverId);
        if ($sender->blockedUserBy->contains($receiver)) {
            return;
        }

        $conversation = $this
            ->conversationService
            ->findConversationByUserIds([$senderId, $receiverId]);

        try {
            DB::beginTransaction();

            $message = $this->messageService->create([
                'from_id' => $senderId,
                'to_id' => $receiverId,
                'message' => $content,
                'conversation_id' => $conversation->id,
                'is_read' => false,
            ]);
            $conversation->updated_at = now();
            $conversation->save();

            DB::commit();

            Redis::publish('chat', json_encode([
                'event' => config('define.events.message_created'),
                'data' => [
                    'conversation_id' => $message->conversation->id,
                    'sender' => new UserResource($sender),
                    'receiver' => new UserResource($receiver),
                    'message_id' => $message->id,
                    'content' => $message->message,
                    'created' => $message->created_at,
                ],
            ]));
        } catch (Exception $ex) {
            DB::rollBack();
            report($ex);
        }
    }
}
