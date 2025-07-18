<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;

class FanTopic extends Model
{
    protected $fillable = [
        'id', 
        'category_id', 
        'name', 
        'slug', 
        'created_at', 
        'updated_at', 
        'meta_keywords', 
        'meta_description', 
        'summary', 
        'is_approved', 
        'content',
        'user_id'
    ];
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(FanPost::class);
    }
    public function user(){
        return $this->hasOne(FanUser::class,'id','user_id');
    }
}
