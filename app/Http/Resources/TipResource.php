<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipResource extends JsonResource
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
            'tip_type' => $this->tip_type,
            'tip_category' => $this->tip_category,
            'reliability_score' => $this->reliability_score,
            'reliability_level' => $this->reliability_level,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),

            // Creator info
            'admin' => [
                'id' => $this->creator->id ?? null,
                'name' => $this->creator->name ?? 'Admin',
            ],
        ];
    }
}
