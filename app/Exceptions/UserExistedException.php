<?php

namespace App\Exceptions;

use Exception;

class UserExistedException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.user_existed'),
            'message' => __('messages.user_existed'),
        ]);
    }
}
