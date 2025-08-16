<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polls\Prediction;
use App\Models\Polls\PredictionEntry;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

class PredictionController extends Controller
{
    /**
     * Get all predictions
     */
    public function getPredictions(Request $request)
    {
        try {
            $query = Prediction::with(['creator'])
                ->active()
                ->orderBy('event_date', 'asc');

            // Filter by type
            if ($request->has('type') && in_array($request->type, ['match', 'tournament', 'player_performance', 'season'])) {
                $query->where('type', $request->type);
            }

            // Filter by resolved status
            if ($request->has('resolved')) {
                $isResolved = $request->resolved === 'true';
                $query->where('is_resolved', $isResolved);
            }

            $predictions = $query->paginate($request->per_page ?? 10);

            // Add user prediction status if authenticated
            $userId = auth()->user()?->id;
            if ($userId) {
                $predictions->getCollection()->transform(function ($prediction) use ($userId) {
                    $prediction->has_user_predicted = $prediction->hasUserPredicted($userId);
                    $prediction->user_prediction = $prediction->getUserPrediction($userId);
                    return $prediction;
                });
            }

            return response()->json([
                'status' => 'success',
                'data' => $predictions->items(),
                'meta' => [
                    'current_page' => $predictions->currentPage(),
                    'per_page' => $predictions->perPage(),
                    'total' => $predictions->total(),
                    'last_page' => $predictions->lastPage(),
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
     * Get a single prediction
     */
    public function getPrediction($id)
    {
        try {
            $prediction = Prediction::with(['creator', 'entries.user'])->findOrFail($id);

            $userId = auth()->user()?->id;
            if ($userId) {
                $prediction->has_user_predicted = $prediction->hasUserPredicted($userId);
                $prediction->user_prediction = $prediction->getUserPrediction($userId);
            }

            return response()->json([
                'status' => 'success',
                'data' => $prediction
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Prediction not found: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create a new prediction (Admin only)
     */
    public function createPrediction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:match,tournament,player_performance,season',
                'home_team' => 'nullable|string|max:255',
                'away_team' => 'nullable|string|max:255',
                'tournament' => 'nullable|string|max:255',
                'event_date' => 'nullable|date',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'prediction_options' => 'required|array',
                'prediction_deadline' => 'nullable|date',
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
                $imagePath = $request->file('image')->store('predictions', 'public');
            }

            $prediction = Prediction::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'home_team' => $request->home_team,
                'away_team' => $request->away_team,
                'tournament' => $request->tournament,
                'event_date' => $request->event_date,
                'image' => $imagePath,
                'prediction_options' => $request->prediction_options,
                'prediction_deadline' => $request->prediction_deadline,
                'created_by' => auth()->user()->id,
            ]);

            $prediction->load(['creator']);

            return response()->json([
                'status' => 'success',
                'message' => 'Prediction created successfully',
                'data' => $prediction
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit a prediction entry
     */
    public function submitPrediction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'prediction_id' => 'required|integer|exists:predictions,id',
                'predicted_result' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $prediction = Prediction::findOrFail($request->prediction_id);

            // Check if prediction is still open
            if (!$prediction->is_open) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This prediction is no longer accepting entries'
                ], 400);
            }

            $userId = auth()->user()->id;

            // Check if user has already made a prediction
            if ($prediction->hasUserPredicted($userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already made a prediction for this event'
                ], 400);
            }

            $entry = PredictionEntry::create([
                'prediction_id' => $request->prediction_id,
                'user_id' => $userId,
                'predicted_result' => $request->predicted_result,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Prediction submitted successfully',
                'data' => $entry
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve a prediction with actual results (Admin only)
     */
    public function resolvePrediction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'prediction_id' => 'required|integer|exists:predictions,id',
                'actual_result' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $prediction = Prediction::findOrFail($request->prediction_id);

            if ($prediction->is_resolved) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This prediction has already been resolved'
                ], 400);
            }

            // Update prediction with actual results
            $prediction->update([
                'actual_result' => $request->actual_result,
                'is_resolved' => true,
            ]);

            // Calculate points for all entries
            $this->calculatePredictionPoints($prediction);

            return response()->json([
                'status' => 'success',
                'message' => 'Prediction resolved successfully',
                'data' => $prediction
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate points for prediction entries
     */
    private function calculatePredictionPoints(Prediction $prediction)
    {
        $entries = $prediction->entries;
        $actualResult = $prediction->actual_result;

        foreach ($entries as $entry) {
            $points = 0;
            $isCorrect = false;

            // Simple scoring logic - you can customize this based on your needs
            if ($prediction->type === 'match') {
                // For match predictions, check if result matches
                if ($entry->predicted_result['winner'] === $actualResult['winner']) {
                    $points += 10;
                    $isCorrect = true;
                }
                if (isset($entry->predicted_result['score']) &&
                    $entry->predicted_result['score'] === $actualResult['score']) {
                    $points += 20; // Bonus for exact score
                }
            }

            $entry->update([
                'points_earned' => $points,
                'is_correct' => $isCorrect,
            ]);
        }
    }

    /**
     * Get prediction leaderboard
     */
    public function getLeaderboard(Request $request)
    {
        try {
            $leaderboard = PredictionEntry::selectRaw('user_id, SUM(points_earned) as total_points, COUNT(*) as total_predictions, COUNT(CASE WHEN is_correct = 1 THEN 1 END) as correct_predictions')
                ->with('user')
                ->groupBy('user_id')
                ->orderBy('total_points', 'desc')
                ->paginate($request->per_page ?? 20);

            return response()->json([
                'status' => 'success',
                'data' => $leaderboard->items(),
                'meta' => [
                    'current_page' => $leaderboard->currentPage(),
                    'per_page' => $leaderboard->perPage(),
                    'total' => $leaderboard->total(),
                    'last_page' => $leaderboard->lastPage(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
