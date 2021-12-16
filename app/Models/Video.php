<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'name',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        "videoable_type",
        "videoable_id",
        "name"
    ];

    public function videoable()
    {
        return $this->morphTo();
    }
}
