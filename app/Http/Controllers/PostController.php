<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Image;
use App\Models\User;
use App\Services\ImageService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

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
            'image' => $request->video?'':'required',
            'video' => $request->image?'':'required',
        ]);

        if($validator->fails()){
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
        if($request->hasFile('image')){
            if (count($images) <= 4){
                foreach ($images as $imageItem) {
                    $imageName = time().'_'.$imageItem->getClientOriginalName();
                    $imageLink = $imageItem->storeAs('upload', $imageName, 'local');
                    $image = Image::create([
                        'link' => $imageLink,
                        'imageable_type' => 'App\\Models\\Post',
                        'imageable_id' => $post->id,
                    ]);
                }
            }
            else{
                return response()->json([
                    'code' => config('response_code.maximum_num_of_images'),
                    'message' => 'Maximum number of images',
                ]);
            }
        }

        $videos = $request->file('video');
        if($request->hasFile('video')){
            if (count($videos) <= 4){
                foreach ($videos as $videoItem) {
                    $videoName = time().'_'.$videoItem->getClientOriginalName();
                    $videoLink = $videoItem->storeAs('upload', $videoName, 'local');
                    $video = Image::create([
                        'link' => $videoLink,
                        'imageable_type' => 'App\\Models\\Post',
                        'imageable_id' => $post->id,
                    ]);
                }
            }
            else{
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
