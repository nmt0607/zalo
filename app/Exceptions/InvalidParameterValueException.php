<?php

namespace App\Exceptions;

use Exception;

class InvalidParameterValueException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.parameter_value_invalid'),
            'message' => __('messages.parameter_value_invalid'),
        ]);
    }
}
