<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollOptionResource extends JsonResource
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
            'option_text' => $this->option_text,
            'option_image' => $this->option_image ? asset('storage/' . $this->option_image) : null,
            'vote_count' => $this->vote_count,
            'percentage' => $this->percentage,
            'order' => $this->order,
        ];
    }
}
