<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetConversationsRequest;
use App\Http\Resources\ConversationCollection;
use App\Services\ConversationService;

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
                $request->index
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
            });

        return (new ConversationCollection($conversations))
            ->additional([
                'code' => config('response_code.ok'),
                'messages' => __('messages.ok'),
                'numNewMessage' => $numUnreadConversations,
            ]);
    }
}
