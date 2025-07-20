<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;
use App\Http\Resources\TournamentResource;

class TournamentScheduleController extends Controller
{
    public function getTournamentSchedule(Request $request, $tournamentCalendarUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/tournamentschedule/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&tmcl='.$tournamentCalendarUuid; 
        
        $p = $base->fetchData($url);

        return response()->json([
            'data' => new TournamentResource($p->data)
        ]);
    }
}
