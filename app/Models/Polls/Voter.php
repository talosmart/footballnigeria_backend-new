<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Voter extends Model
{
    protected $fillable = [
        'id',
        'poll_id',
        'voter_id',
        'vote_type'
    ];

    public function voter()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
