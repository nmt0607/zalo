<?php

namespace App\Exceptions;

use Exception;

class PostNotExistedException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.post_not_existed'),
            'message' => __('messages.post_not_existed'),
        ]);
    }
}
