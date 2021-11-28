<?php

namespace App\Exceptions;

use Exception;

class AccountAlreadyActiveException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.account_already_active'),
            'message' => __('messages.account_already_active'),
        ]);
    }
}
