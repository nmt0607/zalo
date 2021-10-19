<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request) {
        if (Validator::make($request->all(), [
            'phonenumber' => 'required|regex:/^0[0-9]{9}$/'
        ])->fails()) {
            return [
                'code' => 9997,
                'message' => 'Method Is Invalid',
                'data' => ['Số điện thoại không hợp lệ']
            ];
        }

        if (Validator::make($request->all(), [
            'phonenumber' => 'unique:users,phonenumber'
        ])->fails()) {
            return [
                'code' => 9999,
                'message' => 'Exception Error',
                'data' => ['Số điện thoại đã đăng ký']
            ];
        }

        
        if (Validator::make($request->all(), [
            'password' => 'required|alpha_num|between:6,10'
        ])->fails()) {
            return [
                'code' => 9997,
                'message' => 'Method Is Invalid',
                'data' => ['Mật khẩu không hợp lệ']
            ];
        }

        $user = User::create([
            'name' => '',
            'phonenumber' => $request->phonenumber,
            'password' => bcrypt($request->password),
            'state' => 'active',
            'role' => 'user'
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return [
            'code' => 1000,
            'message' => 'OK',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ];

        
    }

    public function logout(){
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $request->email)->first();

        // Check password
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Wong'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
}
