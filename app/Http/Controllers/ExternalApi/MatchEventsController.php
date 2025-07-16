<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class MatchEventsController extends Controller
{
    public function getMatchEvents(Request $request, $fixtureUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/matchevent/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&fx='.$fixtureUuid; 
        
        return $base->fetchData($url);
    }
}
