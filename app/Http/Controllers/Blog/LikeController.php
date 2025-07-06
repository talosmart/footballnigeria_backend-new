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

            $like = Like::where('user_id', $user->id)
                ->where('post_id', $post_id)
                ->first();

            $status = '';
            
            if (!$like) {
                Like::create([
                    'blog_id' => $validated['blog_id'],
                    'user_id' => (Auth::user())->id,
                    'like' => true
                ]);
                $status = 'liked';
            } else {
                if (!$like->like) {
                    $like->like = true;
                    $like->save();
                    $status = 'liked';
                } else {
                    $like->like = false;
                    $like->save();
                    $status = 'unliked';
                }
            }

            return response()->json([
                'status' => 'success',
                'action' => $status,
                'like' => $like->like,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
