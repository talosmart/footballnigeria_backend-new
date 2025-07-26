<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'tags' => $this->tags->pluck('name'), // Assuming you're using Spatie Tags
            'posts_count' => $this->posts()->count(), // Include related post count
            'posts' => $this->posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'featured_image' => $post->featured_image ? url('uploads/' . $post->featured_image) : null,
                    'published_at' => $post->published_at,
                    'content' => $post->content,
                    'category_id' => $post->category_id,
                    'author_id' => $post->author_id,
                    'is_featured_video' => $post->is_featured_video,
                    // Add SEO data
                    'seo' => $post->seo ? [
                        'model_type' => $post->seo->model_type,
                        'model_id' => $post->seo->model_id,
                        'meta_title' => $post->seo->meta_title,
                        'meta_description' => $post->seo->meta_description,
                        'meta_keywords' => $post->seo->meta_keywords,
                        'og_title' => $post->seo->og_title,
                        'og_description' => $post->seo->og_description,
                        'og_image' => $post->seo->og_image ? url('uploads/' . $post->seo->og_image) : null,
                        'twitter_title' => $post->seo->twitter_title,
                        'twitter_description' => $post->seo->twitter_description,
                        'twitter_image' => $post->seo->twitter_image ? url('uploads/' . $post->seo->twitter_image) : null,
                        'structured_data' => $post->seo->structured_data,
                        'seoable_id' => $post->seo->seoable_id,
                        'seoable_type' => $post->seo->seoable_type,
                    ] : null,
                ];
            }),
        ];
    }
}
