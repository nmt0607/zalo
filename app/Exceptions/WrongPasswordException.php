<?php

namespace App\Exceptions;

use Exception;

class WrongPasswordException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.wrong_password'),
            'message' => __('messages.wrong_password'),
        ]);
    }
}
