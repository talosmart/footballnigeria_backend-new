<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class DeletionsController extends Controller
{
    public function getDeletions(Request $request)
    {
        $base = new base();
        $url = 'http://api.performfeeds.com/soccerdata/deletions/1xlnohn926e1k1wfb2xxlwdjjh?_rt=b&_fmt=json&type=person '; 
        
        return $base->fetchData($url);
    }
}
