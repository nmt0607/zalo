<?php

namespace App\Services;

use App\Exceptions\PostNotExistedException;
use App\Models\Post;

class PostService
{
    public function findOrFail($id)
    {
        $post = Post::find($id);
        if ($post === null) {
            throw new PostNotExistedException();
        }

        return $post;
    }

    public function show($id)
    {
        $post = $this->findOrFail($id);
        $listBlockId = auth()->user()->blockDiary->pluck('id')->toArray();
        if (in_array($post->user_id, $listBlockId)) {
            throw new PostNotExistedException();
        }
        $listBlockedById = auth()->user()->blockedDiaryBy->pluck('id')->toArray();
        if (in_array($post->user_id, $listBlockedById)) {
            throw new PostNotExistedException();
        }
        $post->like = $post->like();
        $post->comment = $post->comment();
        $post->is_liked = $post->isLiked();
        $post->images = $post->images;
        $post->videos = $post->videos;
        $post->author = $post->author;
        $post->author->avatar = $post->author->avatar;
        $post->is_blocked = $post->isBlocked();
        $post->can_edit = $post->canEdit();
        $post->can_comment = $post->canComment();

        return $post;
    }

    public function update($id, $attributes = [])
    {
        $post = $this->findOrFail($id);
        $post->update($attributes);

        return $post;
    }

    public function delete($id)
    {
        return Post::destroy($id);
    }

    public function getAllImageIds($id)
    {
        $post = $this->findOrFail($id);

        return $post->images->pluck('id');
    }
}
