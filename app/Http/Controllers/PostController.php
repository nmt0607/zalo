<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Image;
use App\Models\User;
use App\Services\ImageService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    public function show(Request $request)
    {
        return $this->postService->show($request->id);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'image' => $request->video ? '' : 'required',
            'video' => $request->image ? '' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => config('response_code.parameter_not_enough'),
                'message' => 'Parameter is not enough',
            ]);
        }

        $post = Post::create([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        $images = $request->file('image');
        if ($request->hasFile('image')) {
            if (count($images) <= 4) {
                $this->imageService->createMany($images, $post);
            } else {
                return response()->json([
                    'code' => config('response_code.maximum_num_of_images'),
                    'message' => 'Maximum number of images',
                ]);
            }
        }

        $videos = $request->file('video');
        if ($request->hasFile('video')) {
            if (count($videos) <= 4) {
                $this->imageService->createMany($videos, $post);
            } else {
                return response()->json([
                    'code' => config('response_code.maximum_num_of_images'),
                    'message' => 'Maximum number of images',
                ]);
            }
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'id' => $post->id,
            ]
        ]);
    }

    public function update(UpdatePostRequest $request)
    {
        $post = $this->postService->update($request->id, $request->only(['described']));
        $imageDel = $request->image_del;
        $this->imageService->deleteMany($imageDel);

        if ($request->has('image')) {
            $this->imageService->createMany($request->image, $post);
        }

        if ($request->has('video')) {
            // TODO: Save video to Firebase
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
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
