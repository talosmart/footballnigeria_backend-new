<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class SeoData extends Model
{
    use HasTags;
    
    protected $table ='fn_seo_metas';
    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'structured_data',
        'seoable_id',
        'seoable_type'
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
