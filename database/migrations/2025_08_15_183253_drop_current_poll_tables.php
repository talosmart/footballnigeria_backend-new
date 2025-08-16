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
        // Drop existing poll tables
        Schema::dropIfExists('voters');
        Schema::dropIfExists('votees');
        Schema::dropIfExists('polls');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate old structure if needed for rollback
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('sub_question')->nullable();
            $table->timestamps();
        });

        Schema::create('votees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('polls');
            $table->string('name');
            $table->integer('total_positive_vote_count')->nullable();
            $table->integer('total_negative_vote_count')->nullable();
            $table->timestamps();
        });

        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('votee_id')->constrained('votees');
            $table->foreignId('voter_id')->constrained('users');
            $table->boolean('vote_type')->nullable();
            $table->timestamps();
        });
    }
};
