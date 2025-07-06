<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Controllers\ExternalApi\AreasController;
use App\Http\Controllers\ExternalApi\AutomatedCommentaryController;
use App\Http\Controllers\ExternalApi\BasicMatchStatsController;
use App\Http\Controllers\ExternalApi\ContestantParticipationController;
use App\Http\Controllers\ExternalApi\DeletionsController;
use App\Http\Controllers\ExternalApi\DetailedMatchStatsController;
use App\Http\Controllers\ExternalApi\FixturesLiveScoresResultsController;
use App\Http\Controllers\ExternalApi\FixturesResultsController;
use App\Http\Controllers\ExternalApi\MatchEventsController;
use App\Http\Controllers\ExternalApi\MatchPreviewController;
use App\Http\Controllers\ExternalApi\PassMatrixAverageFormationController;
use App\Http\Controllers\ExternalApi\PenaltiesController;
use App\Http\Controllers\ExternalApi\PlayerCareerController;
use App\Http\Controllers\ExternalApi\PlayerContractController;
use App\Http\Controllers\ExternalApi\PossessionsController;
use App\Http\Controllers\ExternalApi\RankingsController;
use App\Http\Controllers\ExternalApi\RefreesController;
use App\Http\Controllers\ExternalApi\SeasonStatsController;
use App\Http\Controllers\ExternalApi\SquadsController;
use App\Http\Controllers\ExternalApi\StandingsController;
use App\Http\Controllers\ExternalApi\TeamsController;
use App\Http\Controllers\ExternalApi\TournamentCalendarController;
use App\Http\Controllers\ExternalApi\TournamentScheduleController;
use App\Http\Controllers\ExternalApi\TransfersController;
use App\Http\Controllers\ExternalApi\TrophiesController;
use App\Http\Controllers\ExternalApi\VenuesController;


class RunExternalApiJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $areasController = new AreasController();
        $automatedCommentaryController = new AutomatedCommentaryController();
        $basicMatchStatsController = new BasicMatchStatsController();
        $contestantParticipationController = new ContestantParticipationController();
        $deletionsController = new DeletionsController();
        $detailedMatchStatsController = new DetailedMatchStatsController();
        $fixturesLiveScoresResultsController = new FixturesLiveScoresResultsController();
        $fixturesResultsController = new FixturesResultsController();
        $matchEventsController = new MatchEventsController();
        $matchPreviewController = new MatchPreviewController();
        $passMatrixAverageFormationController = new PassMatrixAverageFormationController();
        $penaltiesController = new PenaltiesController();
        $playerCareerController = new PlayerCareerController();
        $playerContractController = new PlayerContractController();
        $possessionsController = new PossessionsController();
        $rankingsController = new RankingsController();
        $refreesController = new RefreesController();
        $seasonStatsController = new SeasonStatsController();
        $squadsController = new SquadsController();
        $standingsController = new StandingsController();
        $teamsController = new TeamsController();
        $tournamentCalendarController = new TournamentCalendarController();
        $tournamentScheduleController = new TournamentScheduleController();
        $transfersController = new TransfersController();
        $trophiesController = new TrophiesController();
        $venuesController = new VenuesController();
    
        $areasController->getAreas(request());
        $automatedCommentaryController->getAutomatedCommentary(request());
        $basicMatchStatsController->getBasicMatchStats(request());
        $contestantParticipationController->getContestantParticipation(request());
        $deletionsController->getDeletions(request());
        $detailedMatchStatsController->getDetailedMatchStats(request());
        $fixturesLiveScoresResultsController->getFixturesLiveScoresResults(request());
        $fixturesResultsController->getFixturesResults(request());
        $matchEventsController->getMatchEvents(request());
        $matchPreviewController->getMatchPreview(request());
        $passMatrixAverageFormationController->getPassMatrixAverageFormation(request());
        $penaltiesController->getPenalties(request());
        $playerCareerController->getPlayerCareer(request());
        $playerContractController->getPlayerContract(request());
        $possessionsController->getPossessions(request());
        $rankingsController->getRankings(request());
        $refreesController->getRefrees(request());
        $seasonStatsController->getSeasonStats(request());
        $squadsController->getSquads(request());
        $standingsController->getStandings(request());
        $teamsController->getTeams(request());
        $tournamentCalendarController->getTournamentCalendar(request());
        $tournamentScheduleController->getTournamentSchedule(request());
        $transfersController->getTransfers(request());
        $trophiesController->getTrophies(request());
        $venuesController->getVenues(request());
    }
}

// To schedule this job every hour, add the following to app/Console/Kernel.php:
// protected function schedule(Schedule $schedule)
// {
//     $schedule->job(new \App\Jobs\RunExternalApiMethodsJob)->hourly();
// }