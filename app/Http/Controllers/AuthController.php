<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(SignupRequest $request) {
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
        auth()->user()->currentAccessToken()->delete();

        return [
            'code' => 1000,
            'message' => 'OK',
            'data' => [
                "Đăng xuất thành công"
            ]
        ];

    }

    public function login(Request $request) {
        if (Validator::make($request->all(), [
            'phonenumber' => 'required|regex:/^0[0-9]{9}$/'
        ])->fails()) {
            return [
                'code' => 9997,
                'message' => 'Method Is Invalid',
                'data' => ['Số điện thoại không hợp lệ']
            ];
        }

        // Check phonenumber
        $user = User::where('phonenumber', $request->phonenumber)->first();

        if (!$user) {
            return [
                'code' => 9995,
                'message' => 'User Is Not Validated',
                'data' => [
                    'Số điện thoại chưa được đăng ký'
                ]
            ];
        }

        // Check password
        if (Validator::make($request->all(), [
            'password' => 'required|alpha_num|between:6,10'
        ])->fails()) {
            return [
                'code' => 9997,
                'message' => 'Method Is Invalid',
                'data' => ['Mật khẩu không hợp lệ']
            ];
        }

        if(!Hash::check($request->password, $user->password)) {
            return [
                'code' => 9999,
                'message' => 'Exception Error',
                'data' => ['Mật khẩu không đúng']
            ];
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return [
            'code' => 1000,
            'message' => 'OK',
            'data' => [
                'id' => $user->id,
                'username' => $user->name,
                'token' => $token,
                'avatar' => '',
                'active' => $user->state
            ]
        ];
    }
}
