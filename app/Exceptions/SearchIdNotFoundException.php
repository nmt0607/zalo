<?php

namespace App\Exceptions;

use Exception;

class SearchIdNotFoundException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.search_id_not_found'),
            'message' => __('messages.search_id_not_found'),
        ]);
    }
}
