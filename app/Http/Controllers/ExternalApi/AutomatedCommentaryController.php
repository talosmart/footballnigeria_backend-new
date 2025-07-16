<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class AutomatedCommentaryController extends Controller
{
    public function getAutomatedCommentary(Request $request, $fixtureUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/commentary/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&fx='.$fixtureUuid;
        
        return $base->fetchData($url);
    }
}
