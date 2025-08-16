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
        Schema::table('tips', function (Blueprint $table) {
            // Add poll relationship
            $table->foreignId('poll_id')->nullable()->after('id')->constrained('polls')->onDelete('cascade');

            // Remove standalone betting fields that don't make sense for poll tips
            $table->dropColumn(['match_teams', 'match_date', 'odds', 'recommended_bet']);

            // Update tip_type to be poll-focused
            $table->dropColumn('tip_type');
            $table->enum('tip_type', ['analysis', 'insider_info', 'statistical', 'expert_opinion'])->after('description');

            // Add new poll-specific fields
            $table->string('tip_category')->default('general')->after('tip_type'); // 'performance', 'prediction', 'player_analysis'
            $table->integer('reliability_score')->default(50)->after('tip_category'); // 1-100 reliability score
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips', function (Blueprint $table) {
            $table->dropForeign(['poll_id']);
            $table->dropColumn(['poll_id', 'tip_category', 'reliability_score']);

            // Restore old fields
            $table->string('match_teams')->after('description');
            $table->datetime('match_date')->after('match_teams');
            $table->decimal('odds', 8, 2)->nullable()->after('match_date');
            $table->string('recommended_bet')->after('odds');

            $table->dropColumn('tip_type');
            $table->enum('tip_type', ['win', 'draw', 'over_under', 'both_teams_score', 'correct_score'])->after('description');
        });
    }
};
