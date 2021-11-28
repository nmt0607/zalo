<?php

namespace App\Exceptions;

use Exception;

class CantGetVerifyCodeException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.cant_get_verify_code'),
            'message' => __('messages.cant_get_verify_code'),
        ]);
    }
}
