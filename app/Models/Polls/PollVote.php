<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PollVote extends Model
{
    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'rating_value',
        'prediction_text',
        'additional_data'
    ];

    protected $casts = [
        'rating_value' => 'integer',
        'additional_data' => 'array',
    ];

    /**
     * Get the poll this vote belongs to
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the poll option this vote is for (nullable for rating/prediction polls)
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    /**
     * Get the user who cast this vote
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
