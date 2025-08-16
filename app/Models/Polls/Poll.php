<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Poll extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'poll_type',
        'image',
        'is_active',
        'is_featured',
        'start_date',
        'end_date',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the poll options for this poll
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('order');
    }

    /**
     * Get all votes for this poll
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Get all tips for this poll
     */
    public function tips(): HasMany
    {
        return $this->hasMany(Tip::class);
    }

    /**
     * Get active tips for this poll
     */
    public function activeTips(): HasMany
    {
        return $this->hasMany(Tip::class)->where('is_active', true);
    }

    /**
     * Get featured tips for this poll
     */
    public function featuredTips(): HasMany
    {
        return $this->hasMany(Tip::class)->where('is_featured', true)->where('is_active', true);
    }

    /**
     * Get the user who created this poll
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get total votes count
     */
    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }

    /**
     * Check if user has voted on this poll
     */
    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's vote for this poll
     */
    public function getUserVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }

    /**
     * Check if poll is active and within date range
     */
    public function getIsActiveNowAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active polls
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured polls
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for league polls
     */
    public function scopeLeague($query)
    {
        return $query->where('type', 'league');
    }

    /**
     * Scope for national polls
     */
    public function scopeNational($query)
    {
        return $query->where('type', 'national');
    }
}
