<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\GetUserFriendRequest;
use App\Http\Requests\SetAcceptFriendRequest;
use App\Http\Requests\GetRequestFriendRequest;
use App\Exceptions\UserNotExistedException;
use App\Http\Resources\UserResource;
use Exception;

class FriendController extends Controller
{
    public function getUserFriends(GetUserFriendRequest $request)
    {
        if ($request->user_id) {
            $user = User::findOrFail($request->user_id);
        } else {
            $user = auth()->user();
        }

        $friends = $user->friends()->sortBy('name')->skip($request->index-1)->take($request->count)->values()->all();
        foreach ($friends as $friend) {
            $friend->user_id = $friend->id;
            $friend->user_name = $friend->name;
            $friend->avatarLink = $friend->avatar?->link;
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'friends' => $friends,
                'total' => $user->friends()->count(),
            ],
        ]);
    }

    public function getRequestedFriend(GetUserFriendRequest $request)
    {
        if ($request->user_id) {
            $user = User::findOrFail($request->user_id);
        } else {
            $user = auth()->user();
        }

        $friends = $user->requestedBy()->orderBy('name')->skip($request->index-1)->take($request->count)->get();
        foreach ($friends as $friend) {
            $friend->username = $friend->name;
            $friend->avatarLink = $friend->avatar->link;
            $friend->created = $friend->pivot->created_at;
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'friends' => $friends,
            ],
        ]);
    }

    public function setAcceptFriend(SetAcceptFriendRequest $request)
    {
        $user = User::find($request->user_id);
        if ($user === null) {
            throw new UserNotExistedException();
        }

        if ($request->is_accept == 1) {
            auth()->user()->requestedBy()->where('from_id', $request->user_id)->first()->pivot->update([
                'status' => 2,
            ]);
        } else {
            auth()->user()->requestedBy()->where('from_id', $request->user_id)->first()->pivot->delete();
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }

    public function setRequestFriend(GetRequestFriendRequest $request)
    {
        $user = User::find($request->user_id);
        if ($user === null) {
            throw new UserNotExistedException();
        }
        auth()->user()->request()->attach($request->user_id, ['status' => 1]);
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'requested_friends' => auth()->user()->request->count(),
            ]
        ]);
    }

    public function getSuggestedListFriends(GetUserFriendRequest $request)
    {
        $friendsId = auth()->user()->friends()->pluck('id')->toArray();
        $users = User::whereNotIn('id', $friendsId)->where('id', '!=', auth()->id())->get();
        foreach ($users as $user) {
            $user->user_id = $user->id;
            $user->username = $user->name;
            $user->avatar = $user->avatar;
            $user->same_friends = $user->friends()->pluck('id')->intersect(auth()->user()->friends()->pluck('id'))->count();
        }
        $request->index == 0 ? $request->index = 1 : $request->index;
        $users = $users->sortByDesc('same_friends')->skip($request->index - 1)->take($request->count)->values()->all();
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'list_users' => $users,
            ]
        ]);
    }
}
