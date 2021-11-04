<?php

namespace App\Http\Controllers;

use App\Exceptions\LastPostNotExistException;
use App\Exceptions\PostNotExistedException;
use App\Http\Requests\CheckNewItemRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\ReportPostRequest;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\GetListPostsRequest;
use App\Models\Post;
use App\Models\Image;
use App\Models\Like;
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
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $this->postService->show($request->id)
        ]);
    }

    public function store(CreatePostRequest $request)
    {
        $post = Post::create([
            'content' => $request->described,
            'user_id' => auth()->id(),
        ]);

        $images = $request->file('image');
        if ($request->hasFile('image')) {
            $this->imageService->createMany($images, $post);
        }

        $videos = $request->file('video');
        if ($request->hasFile('video')) {
            $this->imageService->createMany($videos, $post);
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'id' => $post->id,
            ]
        ]);
    }

    public function get_list_posts(GetListPostsRequest $request)
    {
        $last_id = (int)$request->last_id;
        $index = (int)$request->index;
        $last_index = $index - 1;
        $count = (int)$request->count;
        if ($index == 0) {
            $posts_ids = Post::whereIn('user_id', auth()->user()->friends()->pluck('id'))->orderBy('updated_at', 'desc')->take($count)->pluck('id')->toArray();
            $new_items = 0;
        } else {
            $all_ids = Post::whereIn('user_id', auth()->user()->friends()->pluck('id'))->orderBy('updated_at', 'desc')->pluck('id')->toArray();
            if (array_search($last_id, $all_ids) === false) {
                throw new LastPostNotExistException();
            }
            $new_items = array_search($last_id, $all_ids) - $last_index;
            $key = $new_items + $index;
            $posts_ids = array_slice($all_ids, $key, $count);
            // $posts = Post::whereIn('id', $posts_ids);
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'posts' => array_map(function ($id) {
                    return $this->postService->show($id);
                }, $posts_ids),
                'last_id' => end($posts_ids),
                'new_items' => $new_items
            ]
        ]);
    }

    public function check_new_item(CheckNewItemRequest $request)
    {
        $last_id = (int)$request->last_id;
        $category_id = (int)$request->category_id;
        $all_ids = Post::whereIn('user_id', auth()->user()->friends()->pluck('id'))->orderBy('updated_at', 'desc')->pluck('id')->toArray();
        $new_items = array_search($last_id, $all_ids);
        if (array_search($last_id, $all_ids) === false) {
            throw new LastPostNotExistException();
        }
        $posts_ids = array_slice($all_ids, 0, $new_items);
        // $posts = Post::whereIn('id', $posts_ids);

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'new_items' => $new_items,
                'posts' => array_map(function ($id) {
                    return $this->postService->show($id);
                }, $posts_ids),
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

    public function report(ReportPostRequest $request)
    {
        $post = $this->postService->findOrFail($request->id);
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }

    public function like(Request $request)
    {
        $post = $this->postService->findOrFail($request->id);

        if ($post->isReported()) {
            return response()->json([
                'code' => config('response_code.action_done_previously'),
                'message' => __('messages.action_done_previously')
            ]);
        }

        if ($post->isLiked()) {
            Like::where('user_id', auth()->user()->id)->where('post_id', $post->id)->delete();
        } else {
            $like = Like::create([
                'user_id' => auth()->user()->id,
                'post_id' => $post->id
            ]);
        }

        $like = $this->postService->findOrFail($request->id)->like();

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'like' => $like
            ]
        ]);
    }
}
