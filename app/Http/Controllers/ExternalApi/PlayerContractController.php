<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class PlayerContractController extends Controller
{
    public function getPlayerContract(Request $request, $personUuid)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/playercontract/1xlnohn926e1k1wfb2xxlwdjjh/'.$personUuid.'?_rt=b&_fmt=json';
        
        return $base->fetchData($url);
    }
}
