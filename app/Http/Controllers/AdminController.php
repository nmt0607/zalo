<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\GetUserFriendRequest;
use App\Http\Requests\GetRequestFriendRequest;
use App\Exceptions\UserNotExistedException;


class AdminController extends Controller
{
    public function getUserList(GetUserFriendRequest $request)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'code' => 1009,
                'message' => 'Not access',
            ]);
        }
        $users = User::where('role', 'user')->where('state', 'active')->skip($request->index-1)->take($request->count)->get();
        foreach ($users as $user) {
            $user->avatar = $user->avatar;
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $users,
        ]);
    }

    public function deleteUser(GetRequestFriendRequest $request)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'code' => 1009,
                'message' => 'Not access',
            ]);
        }

        $user = User::destroy($request->user_id);
        if ($user == 0) {
            throw new UserNotExistedException();
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);

    }

    public function setUserState(GetRequestFriendRequest $request)
    {
        if(auth()->user()->role != 'admin') {
            return response()->json([
                'code' => 1009,
                'message' => 'Not access',
            ]);
        }
        $user = User::find($request->user_id);
        if ($user === null) {
            throw new UserNotExistedException();
        }
        $user->state == 'active' ? $user->state = 'block' : $user->state = 'active';
        $user->save();
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);

    }

    public function getUserBasicInfo(GetRequestFriendRequest $request)
    {
        $user = User::find($request->user_id);
        if ($user === null) {
            throw new UserNotExistedException();
        }
        $user->avatar = $user->avatar;
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $user,
        ]);

    }

}
