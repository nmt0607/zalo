<?php

namespace App\Policies;

use App\Exceptions\UserNotValidatedException;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Comment $comment)
    {
        if (!$user->isActive()) {
            throw new UserNotValidatedException();
        }

        return $user->id === $comment->user_id
            && $user->blockedBy->contains($comment->post->author);
    }
}
