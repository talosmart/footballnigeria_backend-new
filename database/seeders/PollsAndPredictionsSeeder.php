<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Polls\Poll;
use App\Models\Polls\PollOption;
use App\Models\Polls\Prediction;
use App\Models\Polls\Tip;
use App\Models\User;

class PollsAndPredictionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first admin user for creator
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first(); // Fallback to first user
        }

        // Create sample League Polls
        $leaguePoll1 = Poll::create([
            'title' => 'WHO WAS THE MAN OF THE MATCH?',
            'description' => 'Vote for your choice of player who stood out on the pitch for Nigeria vs South Africa',
            'type' => 'league',
            'poll_type' => 'multiple_choice',
            'is_active' => true,
            'is_featured' => true,
            'created_by' => $admin->id,
        ]);

        // Add options for man of the match poll
        PollOption::create([
            'poll_id' => $leaguePoll1->id,
            'option_text' => 'Osimhen',
            'vote_count' => 45,
            'percentage' => 30.00,
            'order' => 1,
        ]);

        PollOption::create([
            'poll_id' => $leaguePoll1->id,
            'option_text' => 'Victor Moses',
            'vote_count' => 45,
            'percentage' => 30.00,
            'order' => 2,
        ]);

        PollOption::create([
            'poll_id' => $leaguePoll1->id,
            'option_text' => 'Chukwueze',
            'vote_count' => 45,
            'percentage' => 30.00,
            'order' => 3,
        ]);

        // Create celebration poll
        $leaguePoll2 = Poll::create([
            'title' => 'Which of Nigeria\'s second half goals did you celebrate the most?',
            'description' => 'Tell us which goal made you jump off your seat!',
            'type' => 'league',
            'poll_type' => 'multiple_choice',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        PollOption::create([
            'poll_id' => $leaguePoll2->id,
            'option_text' => 'Degen\'s Own Goal',
            'vote_count' => 30,
            'percentage' => 30.00,
            'order' => 1,
        ]);

        PollOption::create([
            'poll_id' => $leaguePoll2->id,
            'option_text' => 'Osimhen Equalizer',
            'vote_count' => 30,
            'percentage' => 30.00,
            'order' => 2,
        ]);

        // Create National Polls
        $nationalPoll1 = Poll::create([
            'title' => 'Which team do you want Nigeria to face in the AFCON?',
            'description' => 'Choose your preferred opponent for the upcoming AFCON tournament',
            'type' => 'national',
            'poll_type' => 'multiple_choice',
            'is_active' => true,
            'is_featured' => true,
            'created_by' => $admin->id,
        ]);

        $teams = ['South Africa', 'Ghana', 'Egypt', 'Algeria'];
        foreach ($teams as $index => $team) {
            PollOption::create([
                'poll_id' => $nationalPoll1->id,
                'option_text' => $team,
                'vote_count' => 30,
                'percentage' => 30.00,
                'order' => $index + 1,
            ]);
        }

        // Create prediction poll
        $predictionPoll = Poll::create([
            'title' => 'Who will win this weekend? Nigeria vs Ghana',
            'description' => 'Make your prediction for the upcoming match',
            'type' => 'national',
            'poll_type' => 'multiple_choice',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $outcomes = ['Home', 'Draw', 'Away'];
        foreach ($outcomes as $index => $outcome) {
            PollOption::create([
                'poll_id' => $predictionPoll->id,
                'option_text' => $outcome,
                'vote_count' => 30,
                'percentage' => 30.00,
                'order' => $index + 1,
            ]);
        }

        // Create rating poll
        $ratingPoll = Poll::create([
            'title' => 'How would you rate Nigeria\'s performance in the Afcon?',
            'description' => 'Rate the team\'s overall performance using stars',
            'type' => 'national',
            'poll_type' => 'rating',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        // Create sample predictions
        $prediction1 = Prediction::create([
            'title' => 'Nigeria vs Ghana Match Result',
            'description' => 'Predict the outcome of this crucial World Cup qualifier',
            'type' => 'match',
            'home_team' => 'Nigeria',
            'away_team' => 'Ghana',
            'tournament' => 'World Cup Qualifiers',
            'event_date' => now()->addDays(7),
            'prediction_options' => [
                'options' => ['Nigeria Win', 'Draw', 'Ghana Win'],
                'score_prediction' => true,
                'goal_scorer' => true
            ],
            'prediction_deadline' => now()->addDays(6),
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $prediction2 = Prediction::create([
            'title' => 'AFCON 2024 Winner Prediction',
            'description' => 'Who will lift the AFCON trophy this year?',
            'type' => 'tournament',
            'tournament' => 'AFCON 2024',
            'event_date' => now()->addMonths(2),
            'prediction_options' => [
                'teams' => ['Nigeria', 'Egypt', 'Morocco', 'Senegal', 'Algeria', 'Cameroon']
            ],
            'prediction_deadline' => now()->addDays(30),
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        // Create sample tips
        $tip1 = Tip::create([
            'title' => 'Nigeria vs Ghana - Both Teams to Score',
            'description' => 'Both teams have strong attacking records and weak defenses. Expect goals from both sides in this high-stakes qualifier.',
            'match_teams' => 'Nigeria vs Ghana',
            'match_date' => now()->addDays(7),
            'odds' => 1.85,
            'tip_type' => 'both_teams_score',
            'recommended_bet' => 'Both Teams to Score - Yes',
            'confidence_level' => 'high',
            'is_featured' => true,
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $tip2 = Tip::create([
            'title' => 'Osimhen to Score Anytime',
            'description' => 'Victor Osimhen has been in fantastic form and is likely to find the net against Ghana\'s defense.',
            'match_teams' => 'Nigeria vs Ghana',
            'match_date' => now()->addDays(7),
            'odds' => 2.10,
            'tip_type' => 'win',
            'recommended_bet' => 'Osimhen Anytime Goalscorer',
            'confidence_level' => 'medium',
            'is_premium' => true,
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $tip3 = Tip::create([
            'title' => 'Over 2.5 Goals in Nigeria vs South Africa',
            'description' => 'Historical data shows these teams produce high-scoring encounters. Both teams need the win.',
            'match_teams' => 'Nigeria vs South Africa',
            'match_date' => now()->addDays(14),
            'odds' => 1.95,
            'tip_type' => 'over_under',
            'recommended_bet' => 'Over 2.5 Goals',
            'confidence_level' => 'medium',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);
    }
}
