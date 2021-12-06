<?php

namespace App\Exceptions;

use Exception;

class UserAlreadyHasThisRoleException extends Exception
{
    public function render()
    {
        return response()->json([
            'code' => config('response_code.user_already_has_this_role'),
            'message' => __('messages.user_already_has_this_role'),
        ]);
    }
}
