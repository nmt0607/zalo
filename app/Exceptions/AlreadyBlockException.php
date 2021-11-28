<?php

namespace App\Exceptions;

use Exception;

class AlreadyBlockException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.already_block'),
            'message' => __('messages.already_block'),
        ]);
    }
}