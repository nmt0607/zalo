<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyBlockException;
use App\Exceptions\AlreadyUnblockException;
use App\Exceptions\CantSelfBlockException;
use App\Exceptions\UserNotExistedException;
use App\Http\Requests\SetBlockDiaryRequest;
use App\Http\Requests\SetBlockUserRequest;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function set_block_user(SetBlockUserRequest $request)
    {
        $user_id = $request->user_id;
        $type = $request->type;

        if ($user_id == auth()->id()) {
            throw new CantSelfBlockException();
        }

        try {
            $user = User::findOrFail($user_id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotExistedException();
        }

        
        $block = Relationship::where('from_id', auth()->id())->where('to_id', $user_id)->where('status', 3)->first();
        
        if ($type == 1) {   
            if (!$block) {
                Relationship::create([
                    'from_id' => auth()->id(),
                    'to_id' => $user_id,
                    'status' => '3',
                ]);
            }
            else {
                throw new AlreadyBlockException();
            }
        }
        else {
            if ($block) {
                $block->delete();
            }
            else {
                throw new AlreadyUnblockException();
            }
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }

    public function set_block_diary(SetBlockDiaryRequest $request)
    {
        $user_id = $request->user_id;
        $type = $request->type;

        if ($user_id == auth()->id()) {
            throw new CantSelfBlockException();
        }

        try {
            $user = User::findOrFail($user_id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotExistedException();
        }

        
        $block = Relationship::where('from_id', auth()->id())->where('to_id', $user_id)->where('status', 4)->first();
        
        if ($type == 1) {   
            if (!$block) {
                Relationship::create([
                    'from_id' => auth()->id(),
                    'to_id' => $user_id,
                    'status' => '4',
                ]);
            }
            else {
                throw new AlreadyBlockException();
            }
        }
        else {
            if ($block) {
                $block->delete();
            }
            else {
                throw new AlreadyUnblockException();
            }
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }
}
