<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Models\Post;
use App\Models\Image;
use App\Models\User;
use App\Models\Comment;
use App\Services\PostService;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    public function getComment(Request $request){
        return $this->commentService->show($request->id);
    }
}
