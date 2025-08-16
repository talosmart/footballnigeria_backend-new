<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    protected $fillable = [
        'poll_id',
        'option_text',
        'option_image',
        'vote_count',
        'percentage',
        'order'
    ];

    protected $casts = [
        'vote_count' => 'integer',
        'percentage' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Get the poll this option belongs to
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get votes for this option
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Update vote count and percentage
     */
    public function updateVoteStats()
    {
        $this->vote_count = $this->votes()->count();
        $totalPollVotes = $this->poll->total_votes;

        if ($totalPollVotes > 0) {
            $this->percentage = round(($this->vote_count / $totalPollVotes) * 100, 2);
        } else {
            $this->percentage = 0;
        }

        $this->save();
    }
}
