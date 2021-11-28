<?php

namespace App\Exceptions;

use Exception;

class VerifyCodeIncorrectException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.verify_code_incorrect'),
            'message' => __('messages.verify_code_incorrect'),
        ]);
    }
}
