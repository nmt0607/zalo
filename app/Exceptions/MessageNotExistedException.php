<?php

namespace App\Exceptions;

use Exception;

class MessageNotExistedException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.message_not_existed'),
            'message' => __('messages.message_not_existed'),
        ]);
    }
}
