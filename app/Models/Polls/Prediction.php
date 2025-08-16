<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Prediction extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'home_team',
        'away_team',
        'tournament',
        'event_date',
        'image',
        'is_active',
        'prediction_options',
        'prediction_deadline',
        'actual_result',
        'is_resolved',
        'created_by'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'prediction_deadline' => 'datetime',
        'prediction_options' => 'array',
        'actual_result' => 'array',
        'is_active' => 'boolean',
        'is_resolved' => 'boolean',
    ];

    /**
     * Get the user who created this prediction
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all entries for this prediction
     */
    public function entries(): HasMany
    {
        return $this->hasMany(PredictionEntry::class);
    }

    /**
     * Check if user has made a prediction
     */
    public function hasUserPredicted($userId)
    {
        return $this->entries()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's prediction entry
     */
    public function getUserPrediction($userId)
    {
        return $this->entries()->where('user_id', $userId)->first();
    }

    /**
     * Check if prediction is still open
     */
    public function getIsOpenAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->is_resolved) {
            return false;
        }

        if ($this->prediction_deadline && now()->gt($this->prediction_deadline)) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active predictions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for unresolved predictions
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
}
