<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Spatie\Tags\HasTags;

class FanPost extends Model
{
    use SoftDeletes, HasTags;

    protected $table = 'fan_posts';
    protected $fillable = [
        'id', 
        'user_id',
        'title', 
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

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function media()
    {
        return $this->hasMany(FanMedia::class,'post_id','id')->orderBy('order');
    }

    public function comments()
    {
        return $this->hasMany(FanComment::class, 'post_id','id');
    }

    public function reactions()
    {
        return $this->morphMany(FanReaction::class, 'reactable');
    }

    public function topic()
    {
        return $this->belongsTo(FanTopic::class,'topic_id','id');
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
