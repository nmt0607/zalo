<?php

namespace App\Services;

use App\Models\Message;

class MessageService
{
    /** @var ConversationService */
    protected $conversationService;

    public function __construct()
    {
        $this->conversationService = app(ConversationService::class);
    }

    public function create(array $attributes = [])
    {
        return Message::create($attributes);
    }

    public function destroy($id)
    {
        return Message::destroy($id);
    }
}
