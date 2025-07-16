<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class ContestantParticipationController extends Controller
{
    public function getContestantParticipation(Request $request)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/contestantparticipation/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&ctst={contestantUUID}';
        
        return $base->fetchData($url);
    }
}
