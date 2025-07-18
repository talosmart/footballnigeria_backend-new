<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'author' => new UserResource($this->whenLoaded('author')),
            'tags' => $this->tags->pluck('name'),
            'featured_image_url' => $this->featured_image ? url('storage/' . $this->featured_image) : null,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at,
            'is_featured_video_url' => $this->is_featured_video ? url('storage/' . $this->is_featured_video) : null,
            'seo' => new SeoDataResource($this->whenLoaded('seo')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
