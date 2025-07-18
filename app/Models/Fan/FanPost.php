<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FanPost extends Model
{
    use SoftDeletes;

    protected $table = 'fan_posts';
    protected $fillable = [
        'id', 
        'user_id', 
        'topic_id', 
        'content', 
        'status', 
        'approved_at', 
        'approved_by', 
        'rejection_reason', 
        'like_count', 
        'comment_count', 
        'created_at', 
        'updated_at', 
        'deleted_at', 
        'category_id',
        'title',
        'tags'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(FanUser::class,'id','user_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(FanMedia::class,'post_id','id')->orderBy('order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FanComment::class,'post_id','id');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(FanReaction::class, 'reactable');
    }
    public function topic(): HasOne
    {
        return $this->hasOne(FanTopic::class,'id','topic_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'category_id');
    }

    public function approver(): HasOne
    {
        return $this->hasOne(User::class, 'approved_by');
    }
}
