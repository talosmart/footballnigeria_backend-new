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
        Schema::create('fan_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('head1')->nullable();
            $table->string('head2')->nullable();
            $table->string('summary')->nullable();
            $table->string('content')->nullable();
            $table->timestamps();
        });

        Schema::create('fan_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('fan_categories');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('summary')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('content')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
        
        Schema::create('fan_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->foreignId('topic_id')->nullable()->constrained('fan_topics');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
       
        Schema::create('fan_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('fan_posts')->cascadeOnDelete();
            $table->enum('type', ['image', 'video', 'gif']);
            $table->string('url');
            $table->string('thumbnail_url')->nullable(); // For video previews
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
       
        Schema::create('fan_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('fan_posts')->cascadeOnDelete();
            $table->text('content');
            $table->unsignedInteger('like_count')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('fan_comments')->onDelete('cascade');
            $table->unsignedInteger('reply_count')->default(0);
            $table->timestamps();
        });
       
        Schema::create('fan_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['user_id', 'reactable_id', 'reactable_type']);
            $table->morphs('reactable'); // For posts OR comments
            $table->enum('type', ['like', 'love', 'laugh', 'wow', 'sad', 'angry']);
            $table->nullableMorphs('target'); // For reacting to reactions
            $table->string('reaction_chain')->nullable()->index(); // 
            $table->timestamps();
        });


        Schema::create('fan_post_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('fan_posts');
            $table->foreignId('moderator_id')->constrained('users');
            $table->enum('action', ['approved', 'rejected']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fan_post');
        Schema::dropIfExists('fan_media');
        Schema::dropIfExists('fan_comments');
        Schema::dropIfExists('fan_reactions');
        Schema::dropIfExists('fan_categories');
        Schema::dropIfExists('fan_topics');
        Schema::dropIfExists('fan_post_approval_logs');
    }
};
