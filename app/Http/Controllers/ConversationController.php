<?php

namespace App\Http\Controllers;

use App\Exceptions\ConversationNotExistedException;
use App\Exceptions\UserNotExistedException;
use App\Http\Requests\GetConversationsRequest;
use App\Http\Requests\GetConversationRequest;
use App\Http\Resources\ConversationCollection;
use App\Http\Requests\DeleteConversationRequest;
use App\Services\ConversationService;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConversationController extends Controller
{
    protected $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public function index(GetConversationsRequest $request)
    {
        $conversations = $this
            ->conversationService
            ->getListConversationsByUserId(
                auth()->id(),
                $request->count,
                $request->index - 1
            );

        $numUnreadConversations = $conversations
            ->reduce(function ($numUnreadConversations, $conversation) {
                if ($this
                    ->conversationService
                    ->checkConversationUnread($conversation, auth()->id())
                ) {
                    return $numUnreadConversations + 1;
                }

                return $numUnreadConversations;
            }) ?? 0;

        return (new ConversationCollection($conversations))
            ->additional([
                'code' => config('response_code.ok'),
                'messages' => __('messages.ok'),
                'numNewMessage' => $numUnreadConversations,
            ]);
    }

    public function getConversation(GetConversationRequest $request)
    {
        if ($request->conversation_id) {
            $conversation = Conversation::findOrFail($request->conversation_id);
            $messages = $conversation->messages()->orderBy('created_at', 'desc')->skip($request->index - 1)->take($request->count)->get();
        } elseif ($request->partner_id) {
            $messages = Message::where(function ($query) use ($request) {
                $query->where('from_id', auth()->id())->where('to_id', $request->partner_id);
            })->orWhere(function ($query) use ($request) {
                $query->where('to_id', auth()->id())->where('from_id', $request->partner_id);
            })->orderBy('created_at', 'desc')->skip($request->index - 1)->take($request->count)->get();
        }

        foreach ($messages as $message) {
            $message->message_id = $message->id;
            $message->unread = $message->is_read;
            $message->sender = $message->sender;
            $message->sender->username = $message->sender->name;
            $message->sender->avatar = $message->sender->avatar;
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'conversation'  => $messages,
            ]

        ]);
    }

    public function delete_conversation(DeleteConversationRequest $request)
    {
        if (isset($request->partner_id)) {
            $conversations = auth()->user()->conversations->pluck('id')->all();
            try {
                $conversations2 = User::findOrFail($request->partner_id)->conversations->pluck('id')->all();
            } catch (ModelNotFoundException $e) {
                throw new UserNotExistedException();
            }
            $intersect = array_intersect($conversations, $conversations2);
            if (sizeof($intersect) == 0) {
                throw new ConversationNotExistedException();
            }
            Conversation::destroy($intersect);
        }

        if (isset($request->conversation_id)) {
            try {
                $conversation = Conversation::findOrFail($request->conversation_id);
            } catch (ModelNotFoundException $e) {
                throw new ConversationNotExistedException();
            }
            if (!in_array(auth()->id(), $conversation->participants->pluck('id')->all())) {
                throw new ConversationNotExistedException();
            }
            $conversation->delete();
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }
}
