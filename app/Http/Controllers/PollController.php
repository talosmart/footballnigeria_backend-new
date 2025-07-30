<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polls\Poll;
use App\Models\Polls\Voter;

class PollController extends Controller
{
    public function createPoll(Request $request){
        try{
            $validate = $request->validate([
                'question' => 'required|string',
                'sub_question' => 'required|string'
            ]);

            $poll = Poll::create([
                'question' => $validate['question'],
                'sub_question' => $validate['sub_question']
            ]);

            if(!$poll){
                return response()->json(['message' => "unable to create poll"]);
            }

            response()->json([
                'message' => "successfully create poll",
                'data' => $poll
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getPoll(Request $request){
        try{
            $poll = Poll::with(['votes.voter'])->all();

            response()->json([
                'status' => "success",
                'data' => $poll
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updatePoll(Request $request, $id){
        try{
            $validate = $request->validate([
                'question' => 'required|string',
                'sub_question' => 'required|string'
            ]);

            $poll = Poll::findOrFail($id);

            $res = $poll->update([
                'question' => $validate['question'],
                'sub_question' => $validate['sub_question']
            ]);

            if(!$res){
                return response()->json(['message' => "unable to update poll"]);
            }

            response()->json([
                'message' => "successfully update poll",
                'data' => $res
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deletePoll($id){
        try{
            $poll = Poll::findOrFail($id);

            if(!$poll){
                return response()->json(['message' => "poll not found"]);
            }

            $poll->delete();

            response()->json([
                'message' => "successfully delete poll"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function pollCaster(Request $request, $id){
        try{
            $validate = $request->validate([
                'vote' => 'required|boolean'
            ]);

            $poll = Poll::findOrFail($id);

            if(!$poll){
                return response()->json(['message' => "poll not found"]);
            }

            $vote = Voter::where([['poll_id', $id], ['voter_id', auth()->user()->id]])->first();

            if(!$vote){
                $cast = Voter::create([
                    'poll_id' => $id,
                    'voter_id' => auth()->user()->id,
                    'vote_type' => $validate['vote']
                ]);

                if(!$cast){
                    return response()->json(['message' => "vote not casted"]);
                }

                if($validate['vote']){
                    $poll->update([
                        'total_positive_vote_count' => $poll->total_positive_vote_count + 1
                    ]);
                } else {
                    $poll->update([
                        'total_negative_vote_count' => $poll->total_negative_vote_count + 1
                    ]);
                }
            }

            $cast = $vote::update([
                'vote_type' => $validate['vote']
            ]);

            if(!$cast){
                return response()->json(['message' => "vote not casted"]);
            }

            if($vote->vote_type && ($validate['vote'] == false)){
                $poll->update([
                    'total_positive_vote_count' => $poll->total_positive_vote_count - 1,
                    'total_negative_vote_count' => $poll->total_negative_vote_count + 1
                ]);
            }

            if(($vote->vote_type == false) && ($validate['vote'] == true)){
                $poll->update([
                    'total_positive_vote_count' => $poll->total_positive_vote_count + 1,
                    'total_negative_vote_count' => $poll->total_negative_vote_count - 1
                ]);
            }

            response()->json([
                'message' => "vote casted successfully",
                'poll' => $this.get_poll()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
