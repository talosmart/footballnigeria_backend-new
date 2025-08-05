<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Voter extends Model
{
    protected $fillable = [
        'id',
        'votee_id',
        'voter_id',
        'vote_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'voter_id', 'id');
    }
}
