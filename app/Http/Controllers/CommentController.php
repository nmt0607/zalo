<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\GetCommentRequest;
use App\Models\Post;
use App\Models\Image;
use App\Models\User;
use App\Models\Comment;
use App\Services\CommentService;
use App\Services\PostService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * @var PostService
     */
    protected $postService;
    protected $commentService;

    /**
     * @var ImageService
     */


    public function __construct(
        PostService $postService,
        CommentService $commentService
    ) {
        $this->postService = $postService;
        $this->commentService = $commentService;
    }

    public function setComment(CreateCommentRequest $request)
    {
        $post = $this->postService->findOrFail($request->id);
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'comment' => $request->comment,
        ]);
        $post->comments = $post->comments()->take($request->count)->get();
        foreach($post->comments as $comment ) {
            $comment->poster = $comment->poster;
            $comment->poster->avatar = $comment->poster->avatar;
        }
        $isBlocked = null;

        if(auth()->user()->blockedBy)
            $isBlocked = in_array($post->user_id, auth()->user()->blockedBy->pluck('id')->toArray());
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $post->comments,
            'is_blocked'=> $isBlocked?1:0,
        ]);
    }


    public function getComment(GetCommentRequest $request){
        $post = $this->postService->findOrFail($request->id);
        $post->comments = $post->comments()->skip($request->index-1)->take($request->count)->get();
        foreach($post->comments as $comment ) {
            $comment->poster = $comment->poster;
            $comment->poster->avatar = $comment->poster->avatar;
        }
        $isBlocked = null;

        if(auth()->user()->blockedBy)
            $isBlocked = in_array($post->user_id, auth()->user()->blockedBy->pluck('id')->toArray());
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $post->comments,
            'is_blocked'=> $isBlocked?1:0,
        ]);
    }

    public function update(UpdateCommentRequest $request)
    {
        $commentId = $request->id_com;
        $comment = $this->commentService->findOrFail($commentId);
        $this->authorize('update', $comment);

        $this->commentService->update(
            $commentId,
            $request->only(['comment'])
        );

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }

    public function destroy(Request $request)
    {
        $commentId = $request->id_com;
        $comment = $this->commentService->findOrFail($commentId);
        $this->authorize('delete', $comment);

        // TODO: handle logic when the post having this comment was blocked
        $this->commentService->delete($commentId);

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }
}
