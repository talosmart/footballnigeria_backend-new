<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Polls\Tip;
use App\Models\Polls\Poll;
use App\Models\User;

class PollTipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            // Create admin if doesn't exist
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@footballnigeria.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]);
        }

        // Get polls to add tips to
        $polls = Poll::all();

        $tipData = [
            // Super Eagles Performance tips
            [
                'title' => 'Osimhen Form Analysis',
                'description' => 'Victor Osimhen has scored in 4 out of his last 5 international appearances. His current form with Napoli shows excellent finishing ability and positional awareness. Expect him to be a key factor in the upcoming matches.',
                'tip_type' => 'analysis',
                'tip_category' => 'player_analysis',
                'reliability_score' => 85,
                'is_featured' => true
            ],
            [
                'title' => 'Defensive Stability Under Peseiro',
                'description' => 'Under Jose Peseiro, the Super Eagles have maintained clean sheets in 60% of matches. The defensive partnership of Troost-Ekong and Ajayi has been solid, making them strong in defensive phases.',
                'tip_type' => 'statistical',
                'tip_category' => 'team_performance',
                'reliability_score' => 78,
                'is_featured' => false
            ],

            // Premier League teams tips
            [
                'title' => 'Arsenal Title Challenge',
                'description' => 'Arsenal\'s squad depth this season is impressive. With key additions and Arteta\'s tactical maturity, they have a genuine shot at the title. Home form at Emirates has been particularly strong.',
                'tip_type' => 'expert_opinion',
                'tip_category' => 'season_prediction',
                'reliability_score' => 72,
                'is_featured' => true
            ],
            [
                'title' => 'Man City Consistency Factor',
                'description' => 'Manchester City\'s ability to peak during crucial periods under Guardiola is well-documented. Their squad rotation system allows them to maintain intensity across all competitions.',
                'tip_type' => 'analysis',
                'tip_category' => 'tactical_insight',
                'reliability_score' => 90,
                'is_featured' => true
            ],

            // NPFL teams tips
            [
                'title' => 'Rivers United Home Advantage',
                'description' => 'Rivers United has lost only 1 home game this season. The Adokiye Amiesimaka Stadium provides a hostile atmosphere for visiting teams. Their set-piece delivery has been particularly effective at home.',
                'tip_type' => 'statistical',
                'tip_category' => 'home_advantage',
                'reliability_score' => 82,
                'is_featured' => false
            ],
            [
                'title' => 'Enyimba Continental Experience',
                'description' => 'Enyimba\'s experience in CAF competitions gives them an edge in high-pressure situations. Their squad has the mental fortitude to perform when it matters most.',
                'tip_type' => 'insider_info',
                'tip_category' => 'experience_factor',
                'reliability_score' => 75,
                'is_featured' => false
            ],

            // Additional expert tips
            [
                'title' => 'Weather Impact on Nigerian Teams',
                'description' => 'Nigerian teams historically perform better in dry conditions. Recent weather patterns suggest this could be a significant factor in upcoming fixture outcomes.',
                'tip_type' => 'expert_opinion',
                'tip_category' => 'environmental_factors',
                'reliability_score' => 65,
                'is_featured' => false
            ],
            [
                'title' => 'Injury Report Updates',
                'description' => 'Key players\' fitness status directly correlates with team performance. Latest medical reports suggest most first-team players are maintaining good fitness levels.',
                'tip_type' => 'insider_info',
                'tip_category' => 'injury_updates',
                'reliability_score' => 88,
                'is_featured' => true
            ]
        ];

        // Distribute tips across polls
        foreach ($polls as $index => $poll) {
            $tipsForPoll = array_slice($tipData, ($index * 2) % count($tipData), 2);

            foreach ($tipsForPoll as $tipInfo) {
                Tip::create([
                    'poll_id' => $poll->id,
                    'title' => $tipInfo['title'],
                    'description' => $tipInfo['description'],
                    'tip_type' => $tipInfo['tip_type'],
                    'tip_category' => $tipInfo['tip_category'],
                    'reliability_score' => $tipInfo['reliability_score'],
                    'is_featured' => $tipInfo['is_featured'],
                    'is_active' => true,
                    'created_by' => $admin->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $this->command->info('Poll-specific tips created successfully!');
    }
}
