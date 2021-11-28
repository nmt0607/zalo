<?php

namespace App\Http\Controllers;

use App\Exceptions\NewPasswordTooSimilarException;
use App\Exceptions\WrongPasswordException;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }

    public function change_password(ChangePasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->password, $user->password)) {
            throw new WrongPasswordException();
        }

        $len_1 = strlen($request->password);
        $longest = '';
        for ($i = 0; $i < $len_1; $i++) {
            for ($j = $len_1 - $i; $j > 0; $j--) {
                $sub = substr($request->password, $i, $j);
                if (strpos($request->new_password, $sub) !== false && strlen($sub) > strlen($longest)) {
                    $longest = $sub;
                    break;
                }
            }
        }

        if (strlen($longest)/strlen($request->new_password)>=0.8) {
            throw new NewPasswordTooSimilarException();
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return [
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ];
    }
}
