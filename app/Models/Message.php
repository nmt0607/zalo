<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_id',
        'to_id',
        'message',
        'is_read',
        'conversation_id',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function getUnreadAttribute()
    {
        return $this->is_read
            ? config('define.message.status.read')
            : config('define.message.status.unread');
    }

    public function sender(){
        return $this->belongsTo(User::class, 'from_id');
    }
}
