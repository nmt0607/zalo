<?php

namespace App\Http\Controllers;

use App\Exceptions\UserAlreadyHasThisRoleException;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\GetUserFriendRequest;
use App\Http\Requests\GetRequestFriendRequest;
use App\Exceptions\UserNotExistedException;
use App\Http\Requests\GetAnalystResultRequest;
use App\Http\Requests\SetRoleRequest;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Like;
use App\Models\Message;
use App\Models\Post;
use Carbon\Carbon;
use DateTime;

class AdminController extends Controller
{
    public function getUserList(GetUserFriendRequest $request)
    {
        if (auth()->user()->role == 'user') {
            return response()->json([
                'code' => config('response_code.not_access'),
                'message' => __('messages.not_access'),
            ]);
        }
        $users = User::where('role', 'user')->skip($request->index - 1)->take($request->count)->get();
        foreach ($users as $user) {
            $user->avatar = $user->avatar;
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $users,
            'total' => User::where('role', 'user')->count(),
        ]);
    }

    public function deleteUser(GetRequestFriendRequest $request)
    {
        if (auth()->user()->role == 'user') {
            return response()->json([
                'code' => config('response_code.not_access'),
                'message' => __('messages.not_access'),
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
        if (auth()->user()->role == 'user') {
            return response()->json([
                'code' => config('response_code.not_access'),
                'message' => __('messages.not_access'),
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
        if (auth()->user()->role == 'user') {
            return response()->json([
                'code' => config('response_code.not_access'),
                'message' => __('messages.not_access'),
            ]);
        }
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

    public function set_role(SetRoleRequest $request)
    {
        if (auth()->user()->role != 'superadmin') {
            return response()->json([
                'code' => config('response_code.not_access'),
                'message' => __('messages.not_access'),
            ]);
        }

        $user = User::find($request->user_id);
        if ($user === null) {
            throw new UserNotExistedException();
        }

        if ($user->role == $request->role) {
            throw new UserAlreadyHasThisRoleException();
        }
        $user->role = $request->role;
        $user->save();
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }

    public function get_analyst_result(GetAnalystResultRequest $request)
    {
        // if (!in_array(auth()->user()->role, ['superadmin', 'admin'])) {
        if (auth()->user()->role != 'superadmin') {
            return response()->json([
                'code' => config('response_code.not_access'),
                'message' => __('messages.not_access'),
            ]);
        }

        $total_users = User::count();
        $total_messages = Message::count();
        $total_posts = Post::count();
        $total_likes = Like::count();
        $total_comments = Comment::count();
        $total_images_and_videos = Image::where('imageable_type', 'App\Models\Post')->count();

        if ($request->days) {
            $users = User::where('created_at', '>', Carbon::now()->subDays($request->days))->count();
            $messages = Message::where('created_at', '>', Carbon::now()->subDays($request->days))->count();
            $posts = Post::where('created_at', '>', Carbon::now()->subDays($request->days))->count();
            $likes = Like::where('created_at', '>', Carbon::now()->subDays($request->days))->count();
            $comments = Comment::where('created_at', '>', Carbon::now()->subDays($request->days))->count();
            $images_and_videos = Image::where('created_at', '>', Carbon::now()->subDays($request->days))->where('imageable_type', 'App\Models\Post')->count();

            return response()->json([
                'code' => config('response_code.ok'),
                'message' => __('messages.ok'),
                'data' => [
                    'users' => $users,
                    'messages' => $messages,
                    'posts' => $posts,
                    'likes' => $likes,
                    'comments' => $comments,
                    'images_and_videos' => $images_and_videos,
                    'total_users' => $total_users,
                    'total_messages' => $total_messages,
                    'total_posts' => $total_posts,
                    'total_likes' => $total_likes,
                    'total_comments' => $total_comments,
                    'total_images_and_videos' => $total_images_and_videos,
                ]
            ]);
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'total_users' => $total_users,
                'total_messages' => $total_messages,
                'total_posts' => $total_posts,
                'total_likes' => $total_likes,
                'total_comments' => $total_comments,
                'total_images_and_videos' => $total_images_and_videos,
            ]
        ]);
    }
}
