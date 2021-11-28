<?php

namespace App\Exceptions;

use Exception;

class CantSelfBlockException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.cant_self_block'),
            'message' => __('messages.cant_self_block'),
        ]);
    }
}
