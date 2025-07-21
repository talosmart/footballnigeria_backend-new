<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FanComment extends Model
{
    protected $table = 'fan_comments';
    protected $fillable = [
        'id', 
        'user_id', 
        'post_id', 
        'content', 
        'like_count', 
        'created_at', 
        'updated_at', 
        'parent_id', 
        'reply_count', 
        'is_approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function post()
    {
        return $this->belongsTo(FanPost::class,'post_id','id');
    }

    public function reactions()
    {
        return $this->morphMany(FanReaction::class, 'reactable');
    }
    
    public function replies()
    {   
        return $this->hasMany(FanReply::class, 'comment_id','id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FanComment::class, 'parent_id');
    }
}
