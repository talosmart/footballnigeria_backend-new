<?php

namespace App\Http\Controllers\Fan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FanPost;
use App\Models\FanReaction;
use App\Events\ReactionUpdated;
use App\Models\FanComment;
use App\Models\FanReply;
use Illuminate\Support\Facades\Validator;

class FanReactionController extends Controller
{
    public function reactToPost( $post_id)
    {
        try{
            $validator = Validator::make(request()->all(), [
                'type' => 'required|in:like,love,laugh,wow,sad,angry'
            ]);
            $post=FanPost::find($post_id);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to reaction type ',
                    'data'=>$validator->errors()
                ]);
                // return laraResponse('error',[])->error();
            }
            if($post==null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No post found  ',
                    'data'=>[]
                ]);
            }
            $existingReaction = FanReaction::where([
                'user_id' => auth()->id(),
                'reactable_id' => $post->id,
                'reactable_type' => FanPost::class,
            ])->first();
        
            // If clicking the same reaction type - remove it (toggle off)
            if ($existingReaction && $existingReaction->type === request()->type) {
                $existingReaction->delete();
                $action = 'removed';
            } 
            // If different reaction exists - update it
            else if ($existingReaction) {
                $existingReaction->update(['type' => request()->type]);
                $action = 'updated';
            }
            // If no existing reaction - create new
            else {
                $existingReaction = FanReaction::create([
                    'user_id' => auth()->id(),
                    'reactable_id' => $post->id,
                    'reactable_type' => FanPost::class,
                    'type' => request()->type
                ]);
                $action = 'added';
            }
        
            // Update reaction count
            $post->update(['like_count' => $post->reactions()->count()]);
            // event(new ReactionUpdated($post));
        
            return response()->json([
                'status' => 'success',
                'action' => $action,
                'reaction' => $action === 'removed' ? null : $existingReaction,
                'total_reactions' => $post->like_count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reactToComment($comment_id, Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:like,love,laugh,wow,sad,angry'
            ]);

            $comment = FanComment::find($comment_id);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid reaction type',
                    'data' => $validator->errors()
                ]);
            }

            if (!$comment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Comment not found',
                    'data' => []
                ]);
            }

            $existingReaction = FanReaction::where([
                'user_id' => auth()->id(),
                'reactable_id' => $comment->id,
                'reactable_type' => FanComment::class,
            ])->first();

            // Toggle logic
            if ($existingReaction) {
                if ($existingReaction->type === $request->type) {
                    $existingReaction->delete();
                    $action = 'removed';
                } else {
                    $existingReaction->update(['type' => $request->type]);
                    $action = 'updated';
                }
            } else {
                $existingReaction = FanReaction::create([
                    'user_id' => auth()->id(),
                    'reactable_id' => $comment->id,
                    'reactable_type' => FanComment::class,
                    'type' => $request->type
                ]);
                $action = 'added';
            }

            $comment->update(['reaction_count' => $comment->reactions()->count()]);
            // event(new ReactionUpdated($comment));

            return response()->json([
                'status' => 'success',
                'action' => $action,
                'reaction' => $action === 'removed' ? null : $existingReaction,
                'total_reactions' => $comment->reaction_count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reactToReply($reply_id, Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:like,love,laugh,wow,sad,angry'
            ]);

            $reply = FanReply::find($reply_id);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid reaction type',
                    'data' => $validator->errors()
                ]);
            }

            if (!$reply) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reply not found',
                    'data' => []
                ]);
            }

            $existingReaction = FanReaction::where([
                'user_id' => auth()->id(),
                'reactable_id' => $reply->id,
                'reactable_type' => FanReply::class,
            ])->first();

            // Toggle logic
            if ($existingReaction) {
                if ($existingReaction->type === $request->type) {
                    $existingReaction->delete();
                    $action = 'removed';
                } else {
                    $existingReaction->update(['type' => $request->type]);
                    $action = 'updated';
                }
            } else {
                $existingReaction = FanReaction::create([
                    'user_id' => auth()->id(),
                    'reactable_id' => $reply->id,
                    'reactable_type' => FanReply::class,
                    'type' => $request->type
                ]);
                $action = 'added';
            }

            $reply->update(['reaction_count' => $reply->reactions()->count()]);
            // event(new ReactionUpdated($reply));

            return response()->json([
                'status' => 'success',
                'action' => $action,
                'reaction' => $action === 'removed' ? null : $existingReaction,
                'total_reactions' => $reply->reaction_count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }    
    }
}
