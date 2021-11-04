<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->where('link', 'not like', '%mp4');
    }

    public function videos()
    {
        return $this->morphMany(Image::class, 'imageable')->where('link', 'like', '%mp4');
    }

    public function like()
    {
        return Like::where('post_id', $this->id)->count();
    }

    public function comment()
    {
        return Comment::where('post_id', $this->id)->count();
    }

    public function isLiked()
    {
        return in_array(auth()->id(), Like::where('post_id', $this->id)->pluck('user_id')->toArray());
    }

    public function isBlocked()
    {
        return in_array(auth()->id(), User::findOrFail($this->user_id)->block->pluck('to_id')->toArray());
    }

    public function canComment()
    {
        if (auth()->id() == $this->user_id) {
            return true;
        } elseif (Relationship::where('from_id', auth()->id())->where('to_id', $this->user_id)->where('status', 2)) {
            return true;
        } elseif (Relationship::where('to_id', auth()->id())->where('from_id', $this->user_id)->where('status', 2)) {
            return true;
        } else {
            return false;
        }
    }

    public function canEdit()
    {
        return auth()->id() == $this->user_id;
    }

    public function isReported()
    {
        return false;
    }
}
