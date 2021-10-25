<?php

namespace App\Exceptions;

use Exception;

class FileTooBigException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.file_too_big'),
            'message' => __('messages.file_too_big'),
        ]);
    }
}
