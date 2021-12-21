<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;

class ConversationService
{
    /**
     * @param int $id
     * @param int $limit
     * @param int $offset
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getListConversationsByUserId($id, $limit = 15, $offset = 0)
    {
        $user = User::findOrFail($id);

        return $user
            ->conversations()
            ->limit($limit)
            ->offset($offset)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Check a conversation being unread by user
     * @param App\Models\Conversation $conversation
     * @param int $userId
     * @return boolean
     */
    public function checkConversationUnread($conversation, $userId)
    {
        // This conversation is being unread by user
        // if one of its message sent to this user is being unread
        return $conversation->messages->contains(function ($message) use ($userId) {
            return $message->to_id === $userId && !$message->is_read;
        });
    }

    public function findConversationByUserIds($userIds)
    {
        return Conversation::with('participants')
            ->get()
            ->first(function ($conversation) use ($userIds) {
                return $conversation->participants->pluck('id')->equal($userIds);
            });
    }

    public function createConversation($userIds)
    {
        $conversation = Conversation::create();
        $conversation->participants()->attach($userIds);

        return $conversation;
    }
}
