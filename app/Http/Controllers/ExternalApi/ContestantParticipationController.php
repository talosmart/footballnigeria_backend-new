<?php

namespace App\Http\Controllers\ExternalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ExternalApi\base;

class ContestantParticipationController extends Controller
{
    function getContestantParticipation(Request $request)
    {
        $base = new base();
        $url = 'enter url here'; // Replace with the actual URL to fetch areas data
        
        return $base->fetchData($url);
    }
}
