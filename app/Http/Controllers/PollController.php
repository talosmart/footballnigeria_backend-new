<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polls\Poll;
use App\Models\Polls\Votee;
use App\Models\Polls\Voter;

class PollController extends Controller
{
    public function createPoll(Request $request){
        try{
            $validate = $request->validate([
                'question' => 'required|string',
                'votees' => "required|array"
            ]);

            $poll = Poll::create([
                'question' => $validate['question']
            ]);
 
            foreach($validate['votees'] as $votee){
                $votees = Votee::create([
                    'poll_id' => $poll->id,
                    'name' =>  $votee['name']
                ]);
            }

            return response()->json([
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
            $poll = Poll::with(['votes.voter.user'])->get();

            return response()->json([
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

    public function updatePoll(Request $request){
        try{
            $validate = $request->validate([
                'id' => 'required|integer',
                'question' => 'required|string',
                'votees' => 'required|array'
            ]);

            $poll = Poll::findOrFail($validate['id']);

            $res = $poll->update([
                'question' => $validate['question']
            ]);

            foreach($validate['votees'] as $votee){
                $votees = Votee::where([['poll_id',  $validate['id']], ['id', $votee['id']]])->first();

                if($votees){
                    $votees->update([
                        'name' =>  $votee['name']
                    ]);
                }
            }


            if(!$res){
                return response()->json(['message' => "unable to update poll"]);
            }

            return response()->json([
                'message' => "successfully update poll"
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

            return response()->json([
                'message' => "successfully delete poll"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function pollCaster(Request $request){
        try{
            $validate = $request->validate([
                'id' => 'required|integer',
                'vote' => 'required|boolean'
            ]);

            $votee = Votee::findOrFail($validate['id']);

            if(!$votee){
                return response()->json(['message' => "poll not found"]);
            }

            $vote = Voter::where([['votee_id', $validate['id']], ['voter_id', auth()->user()->id]])->first();

            if(!$vote){
                $cast = Voter::create([
                    'votee_id' => $validate['id'],
                    'voter_id' => auth()->user()->id,
                    'vote_type' => $validate['vote']
                ]);

                if(!$cast){
                    return response()->json(['message' => "vote not casted"]);
                }

                if($validate['vote']){
                    $votee->update([
                        'total_positive_vote_count' => $votee->total_positive_vote_count + 1
                    ]);
                } else {
                    $votee->update([
                        'total_negative_vote_count' => $votee->total_negative_vote_count + 1
                    ]);
                }
            }

            $cast = $vote->update([
                'vote_type' => $validate['vote']
            ]);

            if(!$cast){
                return response()->json(['message' => "vote not casted"]);
            }

            if($validate['vote'] == false){
                $votee->update([
                    'total_positive_vote_count' => $votee->total_positive_vote_count - 1,
                    'total_negative_vote_count' => $votee->total_negative_vote_count + 1
                ]);
            }else{
                $votee->update([
                    'total_positive_vote_count' => $votee->total_positive_vote_count + 1,
                    'total_negative_vote_count' => $votee->total_negative_vote_count - 1
                ]);
            }

            return response()->json([
                'message' => "vote casted successfully",
                'poll' => $this->getPoll($request)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
