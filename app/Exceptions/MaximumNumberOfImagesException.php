<?php

namespace App\Exceptions;

use Exception;

class MaximumNumberOfImagesException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.maximum_num_of_images'),
            'message' => __('messages.maximum_num_of_images'),
        ]);
    }
}
