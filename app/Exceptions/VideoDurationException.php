<?php

namespace App\Exceptions;

use Exception;

class VideoDurationException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.video_duration'),
            'message' => __('messages.video_duration'),
        ]);
    }
}
