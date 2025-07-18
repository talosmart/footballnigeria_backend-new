<?php

namespace App\Models\Fan;

use Illuminate\Database\Eloquent\Model;

class FanMedia extends Model
{
    protected $table = 'fan_media';
    protected $fillable = [
        'post_id', 
        'type', 
        'url', 
        'thumbnail_url', 
        'order'
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(FanPost::class,'id','post_id');
    }
}
