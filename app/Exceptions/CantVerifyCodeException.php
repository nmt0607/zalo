<?php

namespace App\Exceptions;

use Exception;

class CantVerifyCodeException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.cant_verify_code'),
            'message' => __('messages.cant_verify_code'),
        ]);
    }
}
