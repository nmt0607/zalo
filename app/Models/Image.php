<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'name',
        'imageable_type',
        'imageable_id',
        'type'
    ];

    protected $hidden = [
        'type',
        'updated_at',
        'created_at',
        "imageable_type",
        "imageable_id",
        "name"
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
