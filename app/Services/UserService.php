<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function findOrFail($id)
    {
        return User::findOrFail($id);
    }
}
