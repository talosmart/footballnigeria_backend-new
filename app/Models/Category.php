<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Category extends Model
{
    use HasTags;


    protected $table='fan_categories';
    protected $fillable = [
        'id', 
        'name', 
        'slug', 
        'icon', 
        'created_at', 
        'updated_at', 
        'meta_keywords', 
        'meta_description', 
        'head1', 
        'head2', 
        'summary', 
        'content'
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class,'category_id','id');
    }
}
