<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;

class Votee extends Model
{
    protected $fillable = [
        'id',
        'poll_id',
        'name',
        'total_positive_vote_count',
        'total_negative_vote_count'
    ];

    public function voter()
    {
        return $this->hasMany(Voter::class, 'voter_id', 'id');
    }
}
