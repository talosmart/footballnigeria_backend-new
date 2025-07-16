<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class DetailedMatchStatsController extends Controller
{
    public function getDetailedMatchStats(Request $request, $fixtureUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/matchstats/1xlnohn926e1k1wfb2xxlwdjjh/'.$fixtureUuid.'?_rt=b&_fmt=json&detailed=yes';
        
        return $base->fetchData($url);
    }
}
