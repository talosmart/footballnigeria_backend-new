<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Comment;
use Illuminate\Support\Facades\Auth;
use Exception;

class CommentController extends Controller
{
    public function create_comment(Request $request){
        try{
            $validated = $request->validate([
                'blog_id' => 'required|integer',
                'comment' => 'required|string'
            ]);

            $comment = Comment::create([
                'blog_id' => $validated['blog_id'],
                'user_id' => (Auth::user())->id,
                'comment' => $validated['title']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment created successfully',
                'data' => $comment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update_comment(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|integer|exists:comments,id',
                'blog_id' => 'required|integer',
                'comment' => 'required|string'
            ]);

            $comment = Comment::findOrFail($validated['id']);

            if (!$comment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Comment not found',
                ], 404);
            }

            $comment->update([
                'blog_id' => $validated['blog_id'],
                'user_id' => (Auth::user())->id,
                'comment' => $validated['comment']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment updated successfully',
                'data' => $comment
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update_comment_approval(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'id' => 'required|integer|exists:comments,id',
                ]);

                $comment = Comment::findOrFail($validated['id']);

                if (is_null($comment->is_approved)) {
                    $comment->is_approved = true;
                } elseif ($comment->is_approved === false) {
                    $comment->is_approved = true;
                } else {
                    $comment->is_approved = false;
                }

                $comment->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Comment approval status updated successfully',
                    'data' => $comment
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete_comment(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'id' => 'required|integer|exists:comments,id',
                ]);

                $comment = Comment::findOrFail($validated['id']);

                if (!$comment) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Comment not found',
                    ], 404);
                }

                $comment->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Comment deleted successfully',
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
