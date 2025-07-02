<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class users extends Controller
{
    public function get_all_users(Request $request)
    {
       
        try {
            if (auth()->user()->role === 'admin') {
                $user = User::findOrFail($id);

                return response()->json([
                    'status' => 'success',
                    'data' => $user,
                ], 200);
            };
            
            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            \Log::error('User fetch failed: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'User not found or something went wrong.',
            ], 404);
        }
    }
}
