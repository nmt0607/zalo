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
