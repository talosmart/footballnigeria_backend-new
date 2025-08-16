<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polls\Poll;
use App\Models\Polls\PollOption;
use App\Models\Polls\PollVote;
use App\Http\Resources\PollResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

class PollController extends Controller
{
    /**
     * Get all polls with optional filters
     */
    public function getPolls(Request $request)
    {
        try {
            $query = Poll::with(['options', 'creator', 'activeTips.creator'])
                ->active()
                ->orderBy('created_at', 'desc');

            // Filter by type (league or national)
            if ($request->has('type') && in_array($request->type, ['league', 'national'])) {
                $query->where('type', $request->type);
            }

            // Filter by poll type
            if ($request->has('poll_type') && in_array($request->poll_type, ['multiple_choice', 'rating', 'prediction'])) {
                $query->where('poll_type', $request->poll_type);
            }

            // Filter featured polls
            if ($request->has('featured') && $request->featured == 'true') {
                $query->featured();
            }

            // Filter polls with tips
            if ($request->has('with_tips') && $request->with_tips == 'true') {
                $query->whereHas('activeTips');
            }

            $polls = $query->paginate($request->per_page ?? 10);

            // Add user voting status if authenticated
            $userId = auth()->id();
            if ($userId) {
                $polls->getCollection()->transform(function ($poll) use ($userId) {
                    $poll->has_user_voted = $poll->hasUserVoted($userId);
                    $poll->user_vote = $poll->getUserVote($userId);
                    return $poll;
                });
            }

            return response()->json([
                'status' => 'success',
                'data' => PollResource::collection($polls->items()),
                'meta' => [
                    'current_page' => $polls->currentPage(),
                    'per_page' => $polls->perPage(),
                    'total' => $polls->total(),
                    'last_page' => $polls->lastPage(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get league polls specifically
     */
    public function getLeaguePolls(Request $request)
    {
        $request->merge(['type' => 'league']);
        return $this->getPolls($request);
    }

    /**
     * Get national polls specifically
     */
    public function getNationalPolls(Request $request)
    {
        $request->merge(['type' => 'national']);
        return $this->getPolls($request);
    }

    /**
     * Get a single poll by ID
     */
    public function getPoll($id)
    {
        try {
            $poll = Poll::with(['options', 'creator', 'activeTips.creator'])->findOrFail($id);

            $userId = auth()->id();
            if ($userId) {
                $poll->has_user_voted = $poll->hasUserVoted($userId);
                $poll->user_vote = $poll->getUserVote($userId);
            }

            return response()->json([
                'status' => 'success',
                'data' => new PollResource($poll)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Poll not found: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create a new poll (Admin only)
     */
    public function createPoll(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:league,national',
                'poll_type' => 'required|in:multiple_choice,rating,prediction',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_featured' => 'nullable|boolean',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'options' => 'required_if:poll_type,multiple_choice|array|min:2',
                'options.*.text' => 'required_with:options|string|max:255',
                'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('polls', 'public');
            }

            // Create poll
            $poll = Poll::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'poll_type' => $request->poll_type,
                'image' => $imagePath,
                'is_featured' => $request->is_featured ?? false,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'created_by' => auth()->id(),
            ]);

            // Create options for multiple choice polls
            if ($request->poll_type === 'multiple_choice' && $request->has('options')) {
                foreach ($request->options as $index => $option) {
                    $optionImagePath = null;
                    if (isset($option['image']) && $option['image']) {
                        $optionImagePath = $option['image']->store('poll-options', 'public');
                    }

                    PollOption::create([
                        'poll_id' => $poll->id,
                        'option_text' => $option['text'],
                        'option_image' => $optionImagePath,
                        'order' => $index + 1,
                    ]);
                }
            }

            $poll->load(['options', 'creator']);

            return response()->json([
                'status' => 'success',
                'message' => 'Poll created successfully',
                'data' => $poll
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing poll (Admin only)
     */
    public function updatePoll(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:polls,id',
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'type' => 'sometimes|in:league,national',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_featured' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $poll = Poll::findOrFail($request->id);

            $updateData = $request->only(['title', 'description', 'type', 'is_featured', 'is_active', 'start_date', 'end_date']);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($poll->image) {
                    Storage::disk('public')->delete($poll->image);
                }
                $updateData['image'] = $request->file('image')->store('polls', 'public');
            }

            $poll->update($updateData);
            $poll->load(['options', 'creator']);

            return response()->json([
                'status' => 'success',
                'message' => 'Poll updated successfully',
                'data' => $poll
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a poll (Admin only)
     */
    public function deletePoll($id)
    {
        try {
            $poll = Poll::findOrFail($id);

            // Delete associated images
            if ($poll->image) {
                Storage::disk('public')->delete($poll->image);
            }

            foreach ($poll->options as $option) {
                if ($option->option_image) {
                    Storage::disk('public')->delete($option->option_image);
                }
            }

            $poll->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Poll deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cast a vote on a poll
     */
    public function castVote(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'poll_id' => 'required|integer|exists:polls,id',
                'poll_option_id' => 'required_if:vote_type,option|integer|exists:poll_options,id',
                'rating_value' => 'required_if:vote_type,rating|integer|between:1,5',
                'prediction_text' => 'required_if:vote_type,prediction|string|max:500',
                'vote_type' => 'required|in:option,rating,prediction',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $poll = Poll::findOrFail($request->poll_id);

            // Check if poll is still active
            if (!$poll->is_active_now) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This poll is no longer active'
                ], 400);
            }

            $userId = auth()->id();

            // Check if user has already voted
            if ($poll->hasUserVoted($userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already voted on this poll'
                ], 400);
            }

            // Create vote record
            $voteData = [
                'poll_id' => $request->poll_id,
                'user_id' => $userId,
            ];

            if ($request->vote_type === 'option') {
                $voteData['poll_option_id'] = $request->poll_option_id;

                // Validate that the option belongs to this poll
                $option = PollOption::where('id', $request->poll_option_id)
                    ->where('poll_id', $request->poll_id)
                    ->first();

                if (!$option) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid poll option selected'
                    ], 400);
                }

                // Update option vote count
                $option->increment('vote_count');
            } elseif ($request->vote_type === 'rating') {
                $voteData['rating_value'] = $request->rating_value;
            } elseif ($request->vote_type === 'prediction') {
                $voteData['prediction_text'] = $request->prediction_text;
            }

            $vote = PollVote::create($voteData);

            // Update percentages for all options if this was a multiple choice vote
            if ($request->vote_type === 'option') {
                $this->updateOptionPercentages($poll);
            }

            $poll->load(['options', 'votes']);

            return response()->json([
                'status' => 'success',
                'message' => 'Vote cast successfully',
                'data' => [
                    'vote' => $vote,
                    'poll' => $poll
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update percentages for all options in a poll
     */
    private function updateOptionPercentages($poll)
    {
        // Get fresh option data to ensure we have current vote counts
        $options = PollOption::where('poll_id', $poll->id)->get();
        $totalVotes = $options->sum('vote_count');

        if ($totalVotes > 0) {
            foreach ($options as $option) {
                $percentage = round(($option->vote_count / $totalVotes) * 100, 2);

                // Ensure percentage doesn't exceed 100 and is valid
                $percentage = min(100, max(0, $percentage));

                $option->update(['percentage' => $percentage]);
            }
        } else {
            // If no votes, set all percentages to 0
            $options->each(function ($option) {
                $option->update(['percentage' => 0]);
            });
        }
    }

    /**
     * Get poll statistics
     */
    public function getPollStats($id)
    {
        try {
            $poll = Poll::with(['options', 'votes.user'])->findOrFail($id);

            $stats = [
                'total_votes' => $poll->votes->count(),
                'options_stats' => [],
                'recent_voters' => $poll->votes()->with('user')->latest()->take(10)->get(),
            ];

            if ($poll->poll_type === 'multiple_choice') {
                foreach ($poll->options as $option) {
                    $stats['options_stats'][] = [
                        'option_id' => $option->id,
                        'option_text' => $option->option_text,
                        'vote_count' => $option->vote_count,
                        'percentage' => $option->percentage,
                    ];
                }
            } elseif ($poll->poll_type === 'rating') {
                $ratings = $poll->votes()->whereNotNull('rating_value')->pluck('rating_value');
                $stats['average_rating'] = $ratings->avg();
                $stats['rating_distribution'] = [
                    '1' => $ratings->where('=', 1)->count(),
                    '2' => $ratings->where('=', 2)->count(),
                    '3' => $ratings->where('=', 3)->count(),
                    '4' => $ratings->where('=', 4)->count(),
                    '5' => $ratings->where('=', 5)->count(),
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
