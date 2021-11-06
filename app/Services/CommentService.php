<?php

namespace App\Services;

use App\Models\Comment;

class CommentService
{
    public function show($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->poster = $comment->poster;
        $comment->poster->avatar = $comment->poster->avatar;
        $isBlocked = in_array($comment->post->user_id, auth()->user()->blockedBy->pluck('id')->toArray());
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $comment,
            'is_blocked'=> $isBlocked ? 1 : 0,
        ]);
    }

    public function findOrFail($id)
    {
        return Comment::findOrFail($id);
    }

    public function update($id, $attributes = [])
    {
        $comment = Comment::findOrFail($id);
        $comment->update($attributes);

        return $comment;
    }
}
