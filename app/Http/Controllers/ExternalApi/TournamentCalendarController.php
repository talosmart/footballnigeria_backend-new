<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;
use App\Models\TournamentUrl;
use App\Http\Resources\CompetitionResource;

class TournamentCalendarController extends Controller
{
    public function loadTournamentUrls()
    {
        try{
            $tournamentUrls = TournamentUrl::select('id', 'wp_id', 'slug', 'league_id', 'season_id')->get();

            return response()->json([
                'success' => true,
                'data' => $tournamentUrls,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }    
    }

    public function getTournamentCalendar(Request $request)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/tournamentcalendar/1xlnohn926e1k1wfb2xxlwdjjh/active/authorized?_rt=b&_fmt=json'; 
        
        $p = $base->fetchData($url);

        $data = CompetitionResource::collection($p->data['competition']) ?? json_decode($data, true);

        return laraResponse([
            'data' => $data
        ]);
    }
}
