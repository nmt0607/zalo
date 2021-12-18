<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyBlockException;
use App\Exceptions\LastPostNotExistException;
use App\Exceptions\UserNotExistedException;
use App\Http\Requests\CheckNewItemRequest;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\GetDiaryRequest;
use App\Http\Requests\GetListPostsRequest;
use App\Http\Requests\ReportPostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Services\ImageService;
use App\Services\PostService;
use App\Services\VideoService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /** @var PostService */
    protected $postService;

    /** @var ImageService */
    protected $imageService;

    /** @var VideoService */
    protected $videoService;

    public function __construct(
        PostService $postService,
        ImageService $imageService,
        VideoService $videoService
    ) {
        $this->postService = $postService;
        $this->imageService = $imageService;
        $this->videoService = $videoService;
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

        if ($request->hasFile('image.*')) {
            $this->imageService->createMany($request->image, $post);
        }

        if ($request->hasFile('video')) {
            $this->videoService->create($request->video, $post);
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
        $friend_ids = auth()->user()->friends()->pluck('id');
        $block_ids = auth()->user()->blockDiary->pluck('id');
        $user_ids = $friend_ids->diff($block_ids);
        $user_ids->push(auth()->id());
        if ($index == 0) {
            $posts_ids = Post::whereIn('user_id', $user_ids)->orderBy('updated_at', 'desc')->take($count)->pluck('id')->toArray();
            $new_items = 0;
        } else {
            $all_ids = Post::whereIn('user_id', $user_ids)->orderBy('updated_at', 'desc')->pluck('id')->toArray();
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

    public function get_diary(GetDiaryRequest $request)
    {
        if ($request->user_id) {
            $user_id =  $request->user_id;
            if (!User::find($user_id)) {
                throw new UserNotExistedException();
            }
            if (in_array($user_id, auth()->user()->blockDiary->pluck('id')->toArray())) {
                throw new AlreadyBlockException();
            }
            if (in_array($user_id, auth()->user()->blockedDiaryBy->pluck('id')->toArray())) {
                return response()->json([
                    'code' => config('response_code.ok'),
                    'message' => __('messages.ok'),
                    'data' => [
                        'posts' => [],
                        'last_id' => false,
                        'new_items' => 0
                    ]
                ]);
            }
        } else {
            $user_id = auth()->id();
        }
        $last_id = (int)$request->last_id;
        $index = (int)$request->index;
        $last_index = $index - 1;
        $count = (int)$request->count;
        if ($index == 0) {
            $posts_ids = Post::where('user_id', $user_id)->orderBy('updated_at', 'desc')->take($count)->pluck('id')->toArray();
            $new_items = 0;
        } else {
            $all_ids = Post::where('user_id', $user_id)->orderBy('updated_at', 'desc')->pluck('id')->toArray();
            if (array_search($last_id, $all_ids) === false) {
                throw new LastPostNotExistException();
            }
            $new_items = array_search($last_id, $all_ids) - $last_index;
            $key = $new_items + $index;
            $posts_ids = array_slice($all_ids, $key, $count);
            // $posts = Post::whereIn('id', $posts_ids);
        }

        $posts = [];

        foreach ($posts_ids as $id) {
            $post = $this->postService->show($id);
            $posts[$post->created_at->toDateString()][] = $post;
        }

        $groups_posts = [];
        foreach ($posts as $day => $list_posts) {
            $groups_posts[] = [
                'created_date' => $day,
                'posts' => $list_posts,
            ];
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'groups_posts' => $groups_posts,
                'last_id' => end($posts_ids),
            ]
        ]);
    }

    public function check_new_item(CheckNewItemRequest $request)
    {
        $last_id = (int)$request->last_id;
        $category_id = (int)$request->category_id;
        $friend_ids = auth()->user()->friends()->pluck('id');
        $block_ids = auth()->user()->blockDiary->pluck('id');
        $user_ids = $friend_ids->diff($block_ids);
        $user_ids->push(auth()->id());
        $all_ids = Post::whereIn('user_id', $user_ids)->orderBy('updated_at', 'desc')->pluck('id')->toArray();
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
        $post = $this->postService->update($request->id, [
            'content' => $request->described,
        ]);
        $imageDel = $request->image_del ?? [];
        $this->imageService->deleteMany($imageDel);

        if ($request->hasFile('image.*')) {
            $this->imageService->createMany($request->image, $post);
        }

        if ($request->hasFile('video')) {
            $videoId = $this->postService->getVideoId($post->id);
            $this->videoService->delete($videoId);
            $this->videoService->create($request->video, $post);
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

        // Delete all the images and video that belong to post
        $imageIds = $this->postService->getAllImageIds($postId);
        $videoId = $this->postService->getVideoId($postId);
        $this->imageService->deleteMany($imageIds);
        $this->videoService->delete($videoId);

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
