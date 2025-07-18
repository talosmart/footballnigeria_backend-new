<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;

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

    public function user(): HasOne
    {
        return $this->hasOne(FanUser::class,'id','user_id');
    }

    public function post(): HasOne
    {
        return $this->hasOne(FanPost::class,'id','post_id');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(FanReaction::class, 'reactable');
    }
    public function replies(): HasMany
    {   
        return $this->hasMany(FanReply::class, 'comment_id','id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FanComment::class, 'parent_id');
    }
}
