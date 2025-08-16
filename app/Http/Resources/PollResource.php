<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'poll_type' => $this->poll_type,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_active_now' => $this->is_active_now,
            'total_votes' => $this->total_votes,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relations
            'options' => PollOptionResource::collection($this->whenLoaded('options')),
            'creator' => [
                'id' => $this->creator->id ?? null,
                'name' => $this->creator->name ?? null,
                'email' => $this->creator->email ?? null,
            ],

            // Tips for this poll
            'tips' => TipResource::collection($this->whenLoaded('activeTips')),
            'has_tips' => $this->whenLoaded('activeTips', function() {
                return $this->activeTips->count() > 0;
            }),
            'tips_count' => $this->whenLoaded('activeTips', function() {
                return $this->activeTips->count();
            }),
            'featured_tips_count' => $this->whenLoaded('activeTips', function() {
                return $this->activeTips->where('is_featured', true)->count();
            }),

            // User-specific data (if authenticated)
            'has_user_voted' => $this->when(isset($this->has_user_voted), $this->has_user_voted),
            'user_vote' => $this->when(isset($this->user_vote), $this->user_vote),
        ];
    }
}
