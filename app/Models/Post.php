<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasTags;
    
    protected $table='fn_posts';
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'author_id',
        'featured_image',
        'is_published',
        'published_at',
        'is_featured_video',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
  
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function seo()
    {
        return $this->morphOne(SeoData::class, 'seoable');
    }
}
