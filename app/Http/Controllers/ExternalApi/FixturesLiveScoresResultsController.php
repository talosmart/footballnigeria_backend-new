<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class FixturesLiveScoresResultsController extends Controller
{
    public function getFixturesLiveScoresResults(Request $request, $tournamentCalendarUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/match/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&live=yes&tmcl='.$tournamentCalendarUuid;
        
        return $base->fetchData($url);
    }
}
