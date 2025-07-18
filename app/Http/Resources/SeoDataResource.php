<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoDataResource extends JsonResource
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
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'structured_data' => $this->getStructuredData(),
            'seoable_id' => $this->seoable_id,
            'seoable_type' => $this->seoable_type,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }

    private function getStructuredData(): array
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => $this->meta_title,
            "description" => $this->meta_description,
            "keywords" => $this->meta_keywords,
        
            "publisher" => [
                "@type" => "Organization",
                "name" => "Football Nigeria", // Replace with dynamic organization name
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => asset('storage/logo.png'), // Update with your logo path
                ],
            ],
            "author" => [
                "@type" => "Person",
                "name" => "Author Name", // Replace with dynamic author name
            ],
            "datePublished" => $this->created_at ? $this->created_at->toIso8601String() : null,
            "dateModified" => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            "url" => $this->canonical_url ?? request()->fullUrl(),
        ];
    }
}
