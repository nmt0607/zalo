<?php

namespace App\Exceptions;

use Exception;

class UserNotExistedException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.user_not_existed'),
            'message' => __('messages.user_not_existed'),
        ]);
    }
}
