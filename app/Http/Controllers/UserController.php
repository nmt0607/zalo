<?php

namespace App\Http\Controllers;

use App\Exceptions\NewPasswordTooSimilarException;
use App\Exceptions\WrongPasswordException;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UserNotExistedException;
use App\Http\Requests\SetUserInfoRequest;
use App\Services\ImageService;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @var ImageService
     */
    protected $imageService;

    public function __construct(
        ImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

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

        if (strlen($longest) / strlen($request->new_password) >= 0.8) {
            throw new NewPasswordTooSimilarException();
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return [
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ];
    }

    public function getUserInfo(Request $request)
    {
        if ($request->user_id) {
            $user = User::find($request->user_id);
            if ($user === null) {
                throw new UserNotExistedException();
            }
        } else
            $user = auth()->user();

        $user->avatar = $user->avatar;
        $user->listing = $user->friends()->count();
        $user->created = $user->created_at;
        $friendList = $user->friend->pluck('id')->toArray();
        $friendedByList = $user->friendedBy->pluck('id')->toArray();

        if (in_array(auth()->id(), $friendList) || in_array(auth()->id(), $friendedByList)) {
            $user->is_friend = true;
        } else
            $user->is_friend = false;

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $user,
        ]);
    }

    public function set_user_info(SetUserInfoRequest $request)
    {
        $user = auth()->user();

        if ($request->user_name) {
            $user->name = $request->user_name;
        }
        if ($request->description) {
            $user->description = $request->description;
        }
        if ($request->country) {
            $user->country = $request->country;
        }
        if ($request->link) {
            $user->link = $request->link;
        }
        if ($request->hasFile('avatar')) {
            $this->imageService->create($request->avatar, $user, 'avatar');
        }
        if ($request->hasFile('cover_image')) {
            $this->imageService->create($request->cover_image, $user, 'cover image');
        }
        $user->save();
        $user->avatar = $user->avatar;
        $user->cover_image = $user->coverImage;

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
