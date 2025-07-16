<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class TournamentCalendarController extends Controller
{
    public function getTournamentCalendar(Request $request)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/tournamentcalendar/1xlnohn926e1k1wfb2xxlwdjjh/active/authorized?_rt=b&_fmt=json'; 
        
        return $base->fetchData($url);
    }
}
