<?php

namespace App\Exceptions;

use Exception;

class NotEnoughParameterException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.parameter_not_enough'),
            'message' => __('messages.parameter_not_enough'),
        ]);
    }
}
