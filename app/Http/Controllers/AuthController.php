<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotValidatedException;
use App\Exceptions\WrongPasswordException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $user = User::create([
            'name' => '',
            'phonenumber' => $request->phonenumber,
            'password' => bcrypt($request->password),
            'state' => 'inactive',
            'role' => 'user'
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return [
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ];
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'message' => 'Logged out'
            ]
        ];
    }

    public function login(LoginRequest $request)
    {
        // Check phonenumber
        $user = User::where('phonenumber', $request->phonenumber)->first();

        if (!$user) {
            throw new UserNotValidatedException();
        }

        if (!Hash::check($request->password, $user->password)) {
            throw new WrongPasswordException();
        }

        $user->tokens()->delete();
        $token = $user->createToken('myapptoken')->plainTextToken;

        return [
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'id' => $user->id,
                'username' => $user->name,
                'token' => $token,
                'avatar' => $user->avatar?->link,
                'active' => $user->state
            ]
        ];
    }
}
