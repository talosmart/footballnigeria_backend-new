<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class PlayerCareerController extends Controller
{
    public function getPlayerCareer(Request $request, $personUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/playercareer/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&prsn='.$personUuid;
        
        return $base->fetchData($url);
    }
}
