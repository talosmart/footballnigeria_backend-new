<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FanReaction extends Model
{
    protected $table = 'fan_reactions';
    protected $fillable = [
        'id', 
        'user_id', 
        'reactable_type', 
        'reactable_id', 
        'type', 
        'created_at', 
        'updated_at', 
        'target_type', 
        'target_id', 
        'reaction_chain'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(FanReaction::class, 'target');
    }
}
