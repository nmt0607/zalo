<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'phonenumber',
        'password',
        'state',
        'role',
        'description',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
        'role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    public function avatar()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'avatar')->latest();
    }

    public function coverImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'cover image')->latest();
    }

    public function blockUser()
    {
        return $this->belongsToMany(User::class, 'relationships', 'from_id', 'to_id')->where('status', 3);
    }

    public function blockedUserBy()
    {
        return $this->belongsToMany(User::class, 'relationships', 'to_id', 'from_id')->where('status', 3);
    }

    public function blockUsers()
    {
        return $this->blockUser->merge($this->blockedUserBy);
    }

    public function blockDiary()
    {
        return $this->belongsToMany(User::class, 'relationships', 'from_id', 'to_id')->where('status', 4);
    }

    public function blockedDiaryBy()
    {
        return $this->belongsToMany(User::class, 'relationships', 'to_id', 'from_id')->where('status', 4);
    }

    public function blockDiaries()
    {
        return $this->blockDiary->merge($this->blockedDiaryBy);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function searches()
    {
        return $this->hasMany(Search::class)->latest();
    }

    public function friend()
    {
        return $this->belongsToMany(User::class, 'relationships', 'from_id', 'to_id')->where('status', 2);
    }

    public function friendedBy()
    {
        return $this->belongsToMany(User::class, 'relationships', 'to_id', 'from_id')->where('status', 2);
    }

    public function request()
    {
        return $this->belongsToMany(User::class, 'relationships', 'from_id', 'to_id')->where('status', 1)->withTimestamps();
    }

    public function requestedBy()
    {
        return $this->belongsToMany(User::class, 'relationships', 'to_id', 'from_id')->where('status', 1)->withTimestamps()->withPivot('status');
    }

    public function friends()
    {
        return $this->friend->merge($this->friendedBy);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    public function isActive()
    {
        return $this->state === 'active';
    }
}
