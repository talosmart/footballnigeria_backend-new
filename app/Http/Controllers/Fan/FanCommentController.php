<?php

namespace App\Http\Controllers\Fan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fan\FanPost;
use App\Models\Fan\FanComment;
// use App\Events\NewCommentAdded;
use App\Models\Fan\FanReply;
use App\Models\User;

class FanCommentController extends Controller
{
    public function addComment($post_id,Request $request)
    {
       try{
            $request->validate(['content' => 'required|string|max:1000']);
            $post=FanPost::find($post_id);

            $comment = $post->comments()->create([
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);

            // Update comment count
            $post->increment('comment_count');

            // Broadcast event
            // event(new NewCommentAdded($comment));

            return response()->json([
                'status' => 'success',
                'comment' => $comment->load('user'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function listComments($post_id, Request $request)
    {
        try {
            $currentUserId = auth()->id();
            
            $comments = FanComment::with([
                'user', 
                'replies.user',
                'reactions' => function($query) use ($currentUserId) {
                    $query->where('user_id', $currentUserId);
                },
                'replies.reactions' => function($query) use ($currentUserId) {
                    $query->where('user_id', $currentUserId);
                }
            ])
            ->where('post_id', $post_id)
            ->whereNull('parent_id') // Only top-level comments
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);
    
            // Transform comments and replies to include liked status
            $transformedComments = $comments->getCollection()->map(function($comment) use ($currentUserId) {
                $commentArray = $comment->toArray();
                
                // Check if current user liked the comment
                $commentArray['is_liked'] = $comment->reactions->contains('user_id', $currentUserId);
                unset($commentArray['reactions']);
                
                // Transform replies
                if (isset($commentArray['replies'])) {
                    $commentArray['replies'] = collect($commentArray['replies'])->map(function($reply) use ($currentUserId) {
                        $reply['is_liked'] = collect($reply['reactions'])->contains('user_id', $currentUserId);
                        unset($reply['reactions']);
                        return $reply;
                    })->toArray();
                }
                
                return $commentArray;
            });
    
            return response()->json([
                'status' => 'success',
                'comments' => $transformedComments,
                'pagination' => [
                    'total' => $comments->total(),
                    'per_page' => $comments->perPage(),
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getComment($comment_id)
    {
        try{
            $comment = FanComment::with(['user', 'replies.user'])
                        ->findOrFail($comment_id);

            return response()->json([
                'status' => 'success',
                'comment' => $comment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateComment($comment_id, Request $request)
    {
        try{
            $request->validate(['content' => 'required|string|max:20000']);
            
            $comment = FanComment::where('user_id', auth()->id())
                ->findOrFail($comment_id);

            $comment->update(['content' => $request->content]);

            return lresponse()->json([
                'status' => 'success',
                'comment' => $comment->fresh()->load('user')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteComment($comment_id)
    {
        try{
            $comment = FanComment::where('user_id', auth()->id())
                ->findOrFail($comment_id);

                // return $comment->post;
            // Delete replies first if needed
            $comment->replies()->delete();
            
            // Update post comment count
            $comment->post->decrement('comment_count', $comment->reply_count + 1);
            
            $comment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Comment deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getReply($reply_id)
    {
        try{    
            $reply = FanReply::with(['user'])
                // ->whereNotNull('parent_id')
                ->findOrFail($reply_id);

            return response()->json([
                'status' => 'success',
                'reply' => $reply
            ])->success();
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateReply($reply_id, Request $request)
    {
        try{
            $request->validate(['content' => 'required|string|max:1000']);
            
            $reply = FanReply::where('user_id', auth()->id())
                // ->whereNotNull('parent_id')
                ->findOrFail($reply_id);

            $reply->update(['content' => $request->content]);

            return response()->json([
                'status' => 'success',
                'reply' => $reply->fresh()->load('user')
            ])->success();
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteReply($reply_id)
    {
        try{
            $reply = FanReply::where('user_id', auth()->id())
                    // ->whereNotNull('parent_id')
                    ->findOrFail($reply_id);

            $reply->comment->decrement('reply_count');
            $reply->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Reply deleted successfully'
            ])->success();
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function addCommentReply($comment_id,Request $request)
    {
        try{
            $request->validate(['content' => 'required|string|max:1000']);
            $comment=FanComment::find($comment_id);

            $reply = $comment->replies()->create([
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);

            // Update comment count
            $comment->increment('reply_count');

            // Broadcast event
            // event(new NewCommentAdded($comment));

            return response()->json([
                'status' => 'success',
                'comment' => $comment->load('user'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
