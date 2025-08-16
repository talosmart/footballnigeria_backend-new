<?php

namespace App\Http\Controllers;

use App\Models\Polls\Tip;
use App\Models\Polls\Poll;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function getPollTips(Request $request, $pollId)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'TipController getPollTips method is working',
            'poll_id' => $pollId
        ]);
    }

    public function createPollTip(Request $request, $pollId)
    {
        // Simple implementation for testing
        return response()->json([
            'status' => 'success',
            'message' => 'Create poll tip endpoint works'
        ]);
    }

    public function getAllTips(Request $request)
    {
        // Simple implementation for testing
        return response()->json([
            'status' => 'success', 
            'message' => 'Get all tips endpoint works'
        ]);
    }
}
