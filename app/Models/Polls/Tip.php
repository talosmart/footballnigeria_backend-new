<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Tip extends Model
{
    protected $fillable = [
        'poll_id',
        'title',
        'description',
        'tip_type',
        'tip_category',
        'reliability_score',
        'is_featured',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'reliability_score' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the poll this tip belongs to
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the admin who created this tip
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active tips
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured tips
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for tips of a specific poll
     */
    public function scopeForPoll($query, $pollId)
    {
        return $query->where('poll_id', $pollId);
    }

    /**
     * Scope for high reliability tips
     */
    public function scopeHighReliability($query, $threshold = 70)
    {
        return $query->where('reliability_score', '>=', $threshold);
    }

    /**
     * Get reliability level as text
     */
    public function getReliabilityLevelAttribute()
    {
        if ($this->reliability_score >= 80) return 'High';
        if ($this->reliability_score >= 60) return 'Medium';
        return 'Low';
    }

    /**
     * Check if tip is reliable
     */
    public function getIsReliableAttribute()
    {
        return $this->reliability_score >= 70;
    }
}
