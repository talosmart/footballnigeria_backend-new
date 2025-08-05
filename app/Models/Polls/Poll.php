<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'id',
        'question',
        'sub_question'
    ];

    public function votes()
    {
        return $this->hasMany(Votee::class, 'poll_id', 'id');
    }
}
