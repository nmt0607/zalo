<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\ImageService;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @var PostService
     */
    protected $postService;

    /**
     * @var ImageService
     */
    protected $imageService;

    public function __construct(
        PostService $postService,
        ImageService $imageService
    ) {
        $this->postService = $postService;
        $this->imageService = $imageService;
    }

    public function index()
    {
        return response()->json([
            'data' => Post::all(),
        ]);
    }

    public function show($id)
    {
        return Post::find($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        return Post::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);
        return Post::find($id)->update($request->all());
    }

    public function destroy(Request $request)
    {
        $postId = $request->id;
        $post = $this->postService->findOrFail($postId);
        $this->authorize('destroy', $post);

        // Delete all the images that belong to post
        $imageIds = $this->postService->getAllImageIds($postId);
        $this->imageService->deleteMany($imageIds);

        $this->postService->delete($postId);

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }
}
