<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index()->name('blogs_user_id_foreign');
            $table->string('title');
            $table->string('content');
            $table->string('media');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade')->index()->name('likes_blog_id_foreign');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index()->name('likes_user_id_foreign');
            $table->boolean('like')->default(false);
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade')->index()->name('comments_blog_id_foreign');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index()->name('comments_user_id_foreign');
            $table->boolean('is_approved')->default(false);
            $table->string('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('categories');
    }
};
