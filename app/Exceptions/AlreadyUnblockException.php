<?php

namespace App\Exceptions;

use Exception;

class AlreadyUnblockException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.already_unblock'),
            'message' => __('messages.already_unblock'),
        ]);
    }
}
