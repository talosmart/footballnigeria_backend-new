<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'blog_id',
        'user_id',
        'like'
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
