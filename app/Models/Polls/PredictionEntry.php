<?php

namespace App\Models\Polls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PredictionEntry extends Model
{
    protected $fillable = [
        'prediction_id',
        'user_id',
        'predicted_result',
        'points_earned',
        'is_correct'
    ];

    protected $casts = [
        'predicted_result' => 'array',
        'points_earned' => 'integer',
        'is_correct' => 'boolean',
    ];

    /**
     * Get the prediction this entry belongs to
     */
    public function prediction(): BelongsTo
    {
        return $this->belongsTo(Prediction::class);
    }

    /**
     * Get the user who made this prediction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
