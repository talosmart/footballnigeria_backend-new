<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentUrl extends Model
{
    protected $fillable = [
        'category_id',
        'wp_id',
        'slug',
        'season_id',
        'league_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
