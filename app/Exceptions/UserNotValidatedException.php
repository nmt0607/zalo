<?php

namespace App\Exceptions;

use Exception;

class UserNotValidatedException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.user_not_validated'),
            'message' => __('messages.user_not_validated'),
        ]);
    }
}
