<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
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
        return $this->commentService->show($comment->id);
    }

    public function getComment(Request $request)
    {
        return $this->commentService->show($request->id);
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
