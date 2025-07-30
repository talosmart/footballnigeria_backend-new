<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'id',
        'question',
        'sub_question',
        'total_positive_vote_count',
        'total_negative_vote_count'
    ];

    public function votes()
    {
        return $this->hasMany(Voter::class, 'poll_id', 'id');
    }
}
