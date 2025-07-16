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
        $url = 'enter url here'; // Replace with the actual URL to fetch areas data
        
        return $base->fetchData($url);
    }
}
