<?php

namespace App\Exceptions;

use Exception;

class NewPasswordTooSimilarException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.new_password_too_similar'),
            'message' => __('messages.new_password_too_similar'),
        ]);
    }
}
