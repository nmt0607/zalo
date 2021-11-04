<?php

namespace App\Exceptions;

use Exception;

class LastPostNotExistException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.last_post_not_exist'),
            'message' => __('messages.last_post_not_exist'),
        ]);
    }
}
