<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Like;
use Exception;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like_and_unlike_post(Request $request){
        try{
            $validated = $request->validate([
                'blog_id' => 'required|integer'
            ]);

            $data = Like::where('user_id', (Auth::user())->id)
                ->where('blog_id', $validated['blog_id'])
                ->first();

            $status = '';
            
            if (!$data) {
                Like::create([
                    'blog_id' => $validated['blog_id'],
                    'user_id' => (Auth::user())->id,
                    'like' => true
                ]);
                $status = 'liked';
            } else {
                if (!$data->like) {
                    $data->like = true;
                    $data->save();
                    $status = 'liked';
                } else {
                    $data->like = false;
                    $data->save();
                    $status = 'unliked';
                }
            }

            return response()->json([
                'status' => 'success',
                'action' => $status,
                'like' => $data->like,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
