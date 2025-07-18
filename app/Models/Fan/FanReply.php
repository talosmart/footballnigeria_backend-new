<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;

class FanReply extends Model
{
    protected $table = 'fan_replies';
    protected $fillable = [
        'id', 
        'user_id', 
        'post_id', 
        'comment_id', 
        'is_approved', 
        'content', 
        'like_count', 
        'created_at', 
        'updated_at'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(FanUser::class,'id','user_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(FanPost::class,'id','post_id');
    }

    public function comment(): HasOne
    {
        return $this->hasOne(FanComment::class,'id','comment_id');
    }
    public function reactions(): MorphMany
    {
        return $this->morphMany(FanReaction::class, 'reactable');
    }
}
