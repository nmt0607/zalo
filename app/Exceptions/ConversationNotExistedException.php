<?php

namespace App\Exceptions;

use Exception;

class ConversationNotExistedException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.conversation_not_existed'),
            'message' => __('messages.conversation_not_existed'),
        ]);
    }
}
