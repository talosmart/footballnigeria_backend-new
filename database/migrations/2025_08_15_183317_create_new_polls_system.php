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
        // Polls table - main poll questions
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['league', 'national']); // League Poll or National Poll
            $table->enum('poll_type', ['multiple_choice', 'rating', 'prediction']); // Type of poll
            $table->string('image')->nullable(); // Background image for poll
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Poll options table - choices for multiple choice polls
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('polls')->onDelete('cascade');
            $table->string('option_text');
            $table->string('option_image')->nullable();
            $table->integer('vote_count')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Poll votes table - user votes
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('polls')->onDelete('cascade');
            $table->foreignId('poll_option_id')->nullable()->constrained('poll_options')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating_value')->nullable(); // For star ratings (1-5)
            $table->text('prediction_text')->nullable(); // For predictions
            $table->json('additional_data')->nullable(); // For storing extra data
            $table->timestamps();

            // Ensure user can only vote once per poll
            $table->unique(['poll_id', 'user_id']);
        });

        // Predictions table - for match/event predictions
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['match', 'tournament', 'player_performance', 'season']);
            $table->string('home_team')->nullable();
            $table->string('away_team')->nullable();
            $table->string('tournament')->nullable();
            $table->datetime('event_date')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('prediction_options'); // Store prediction options as JSON
            $table->datetime('prediction_deadline')->nullable();
            $table->json('actual_result')->nullable(); // Store actual results
            $table->boolean('is_resolved')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Prediction entries table - user predictions
        Schema::create('prediction_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prediction_id')->constrained('predictions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('predicted_result'); // Store user's prediction
            $table->integer('points_earned')->default(0);
            $table->boolean('is_correct')->nullable();
            $table->timestamps();

            // Ensure user can only predict once per prediction
            $table->unique(['prediction_id', 'user_id']);
        });

        // Tips table - for betting tips
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('match_teams');
            $table->datetime('match_date');
            $table->decimal('odds', 5, 2)->nullable();
            $table->enum('tip_type', ['win', 'draw', 'over_under', 'both_teams_score', 'correct_score']);
            $table->string('recommended_bet');
            $table->enum('confidence_level', ['low', 'medium', 'high']);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['pending', 'won', 'lost', 'void'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tips');
        Schema::dropIfExists('prediction_entries');
        Schema::dropIfExists('predictions');
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
    }
};
