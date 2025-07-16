<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class PenaltiesController extends Controller
{
    public function getPenalties(Request $request, $fixtureUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/matchpenaltiespreview/1xlnohn926e1k1wfb2xxlwdjjh/'.$fixtureUuid.'?_rt=b&_fmt=json';
        
        return $base->fetchData($url);
    }
}
