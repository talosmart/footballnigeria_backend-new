<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class SeasonStatsController extends Controller
{
    public function getSeasonStats(Request $request, $tournamentCalendarUuid, $contestantUUID)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/seasonstats/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&detailed=no&tmcl='.$tournamentCalendarUuid.'&ctst='.$contestantUUID; 
        
        return $base->fetchData($url);
    }
}
