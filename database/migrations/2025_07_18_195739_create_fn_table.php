<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create posts table
        Schema::create('fn_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('category_id')->constrained('fn_categories')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users');
            $table->boolean('is_featured_video')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Create categories table
        Schema::create('fn_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create category_post pivot table
        Schema::create('fn_category_post', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained('fn_categories')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('fn_posts')->cascadeOnDelete();
            $table->primary(['category_id', 'post_id']);
        });

        // Create seo_metas table
        Schema::create('fn_seo_metas', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // Polymorphic relationship
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('structured_data')->nullable();
            $table->integer('seoable_id')->nullable();
            $table->string('seoable_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fn_seo_metas');
        Schema::dropIfExists('fn_category_post');
        Schema::dropIfExists('fn_categories');
        Schema::dropIfExists('fn_posts');
    }
};
