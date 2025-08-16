<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Polls\Poll;
use App\Models\Polls\PollOption;

echo "Recalculating percentages for all polls...\n";

$polls = Poll::with('options')->get();

foreach ($polls as $poll) {
    $options = $poll->options;
    $totalVotes = $options->sum('vote_count');

    if ($totalVotes > 0) {
        foreach ($options as $option) {
            $percentage = round(($option->vote_count / $totalVotes) * 100, 2);
            $percentage = min(100, max(0, $percentage)); // Ensure valid range
            $option->update(['percentage' => $percentage]);

            echo "Poll {$poll->id} - Option {$option->id}: {$option->vote_count} votes = {$percentage}%\n";
        }
    } else {
        foreach ($options as $option) {
            $option->update(['percentage' => 0]);
            echo "Poll {$poll->id} - Option {$option->id}: No votes = 0%\n";
        }
    }
}

echo "Percentages recalculated successfully!\n";
