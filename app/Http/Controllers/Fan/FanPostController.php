<?php

namespace App\Http\Controllers\Fan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FanPost;
use App\Models\FanMedia;
use App\Events\NewPostCreated;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use App\Models\FanTopic;
// use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class FanPostController extends Controller
{
    public function createPost(Request $request)
    {
        try{
            $request->validate([
                'topic_id' => 'nullable',
                'title' => 'required|string|max:20000',
                'content' => 'required|string|max:20000',
                'media' => 'nullable|array',
                'media.*' => 'file',
            ]);
            $s=[
                'user_id' => auth()->id(),
                'content' => $request->content,
                // 'topic_id'
                'title'=>$request->title
            ];
            if(request()->has('topic_id')){
                $s['topic_id']=request()->topic_id;
            }

            // Create post
            $post = FanPost::create($s);

            // Handle media uploads
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $index => $file) {
                    $path = Storage::putFile("fan_posts/{$post->id}/media", $file);
                    $mediaType = $this->getMediaType($file);

                    $thumbnailUrl = null;
                    if ($mediaType === 'video') {
                        $thumbnailUrl = $this->generateVideoThumbnail($file, $post->id);
                    }

                    FanMedia::create([
                        'post_id' => $post->id,
                        'type' => $mediaType,
                        'url' => $path,
                        'thumbnail_url' => $thumbnailUrl,
                        'order' => $index,
                    ]);
                }
            }

            // event(new NewPostCreated($post));

            return response()->json([
                    'status' => 'success',
                    'post' => $post->load(['user', 'media','topic']),
                ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }    
    }



    public function getPost($post_id)
    {
        try {
            $post = FanPost::with([
                'user', 
                'media',
                'topic',
                'comments.user',
                'comments.replies.user',
                'reactions.user'
            ])->findOrFail($post_id);

            return response()->json([
                'status' => 'success',
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updatePost(Request $request, $id)
    {
        $request->validate([

            'title' => 'sometimes|required|string|max:20000',
            'content' => 'sometimes|required|string|max:20000',
            'media' => 'sometimes|nullable|array',
            'media.*' => 'file',
        ]);

        try {
            $post = FanPost::findOrFail($id);

            // Verify ownership if needed
            if ($post->user_id !== auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update this post'
                ]);
            }

            $post->update([
                'content' => $request->input('content', $post->content),
                'title'=>$request->input('title',$post->title)
            ]);

            // Handle media updates if provided
            if ($request->hasFile('media')) {
                // Delete existing media if needed
                $post->media()->delete();

                foreach ($request->file('media') as $index => $file) {
                    $path = Storage::putFile("fan_posts/{$post->id}/media", $file);
                    $mediaType = $this->getMediaType($file);

                    $thumbnailUrl = null;
                    if ($mediaType === 'video') {
                        $thumbnailUrl = $this->generateVideoThumbnail($file, $post->id);
                    }

                    FanMedia::create([
                        'post_id' => $post->id,
                        'type' => $mediaType,
                        'url' => $path,
                        'thumbnail_url' => $thumbnailUrl,
                        'order' => $index,
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'post' => $post->fresh()->load(['user', 'media']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deletePost($id)
    {
        try {
            $post = FanPost::findOrFail($id);

            // Verify ownership if needed
            if ($post->user_id !== auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to delete this post'
                ]);
            }

            // Delete associated media files from storage
            foreach ($post->media as $media) {
                Storage::delete($media->url);
                if ($media->thumbnail_url) {
                    Storage::delete($media->thumbnail_url);
                }
            }

            $post->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function listPosts(Request $request)
    {
        try {
            $currentUserId = auth()->id();
            
            $query = FanPost::with([
                'user', 
                'media',
                'reactions' => function($query) use ($currentUserId) {
                    $query->where('user_id', $currentUserId);
                }
            ])
            ->orderBy('created_at', 'desc');
    
            // Add filters if needed
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
    
            if ($request->has('topic_id')) {
                $query->where('topic_id', $request->topic_id);
            }
    
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
    
            $posts = $query->paginate($request->per_page ?? 15);
    
            // Transform the posts to include liked status
            $transformedPosts = $posts->getCollection()->map(function($post) use ($currentUserId) {
                $postArray = $post->toArray();
                $postArray['is_liked'] = $post->reactions->contains('user_id', $currentUserId);
                unset($postArray['reactions']); // Remove the reactions array if you don't need it
                return $postArray;
            });
    
            return response()->json([
                'status' => 'success',
                'posts' => $transformedPosts,
                'pagination' => [
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function postcategories()
    {
        try{
            $postcategories = \App\Models\Category::all();

            return response()->json([
                'success' => true,
                'message' => 'Post categories Loaded  successfully',
                'data' => $postcategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createTopic(Request $request)
    {
        // Validate the input

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:fan_categories,id',
            'name' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
        ]);


        // Add custom validation for unique slug per category
        $validator->after(function ($validator) use ($request) {
            if ($request->has('name') && $request->has('category_id')) {
                $slug = Str::slug($request->name);
                $exists = FanTopic::where('category_id', $request->category_id)
                    ->where('slug', $slug)
                    ->exists();

                if ($exists) {
                    $slug = $slug . '-' . \Str::random(4);
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Create the topic
            $topic = FanTopic::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'summary' => $request->summary,
                'user_id' => auth()->id() ?? null // Include if you have auth
            ]);

            return response()->json('success', [
                'success' => true,
                'message' => 'Topic created successfully',
                'data' => $topic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function updateTopic(Request $request, $id)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [

            'category_id' => 'sometimes|required|integer|exists:fan_categories,id',
            'name' => 'sometimes|required|string|max:255',
            'summary' => 'sometimes|required|string|max:500',
        ]);

        // Add custom validation for unique slug per category
        $validator->after(function ($validator) use ($request, $id) {
            if ($request->has('name') && $request->has('category_id')) {
                $slug = Str::slug($request->name);
                $exists = FanTopic::where('category_id', $request->category_id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $id)
                    ->where('user_id', auth()->id())
                    ->exists();

                if ($exists) {
                    $slug = $slug . '-' . \Str::random(4);
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ]);
        }

        try {
            $topic = FanTopic::findOrFail($id);

            $updateData = [];
            if ($request->has('category_id')) {
                $updateData['category_id'] = $request->category_id;
            }
            if ($request->has('name')) {
                $updateData['name'] = $request->name;
                $updateData['slug'] = Str::slug($request->name);
            }
            if ($request->has('summary')) {
                $updateData['summary'] = $request->summary;
            }

            $topic->update($updateData);
            $transformed = collect($topic)->map(function ($value, $key) {
                if ($key === 'is_approved') {
                    return $value == '1' ? 'approved' : 'pending';
                }
                return $value;
            });
            return response()->json('success', [
                'success' => true,
                'message' => 'Topic updated successfully',
                'data' => $transformed
            ])->success();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ListTopic(Request $request)
    {
        try {
            // Validate the optional filters
            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|in:approved,unapproved,all',
                'ownership' => 'sometimes|in:mine,all'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ]);
            }

            $status = $request->input('status', 'all');
            $ownership = $request->input('ownership', 'all');
            $userId = auth()->id();

            $query = FanTopic::with(['category', 'user'])
                ->orderBy('created_at', 'desc');

            // Apply approval status filter
            switch ($status) {
                case 'approved':
                    $query->where('is_approved', true);
                    break;
                case 'unapproved':
                    $query->where('is_approved', false);
                    break;
            }

            // Apply ownership filter
            if ($ownership === 'mine' && $userId) {
                $query->where('user_id', $userId);
            }

            $topics = $query->paginate(15);
            // Transform the items collection
            $transformed = $topics->getCollection()->map(function ($topic) {
                return collect($topic)->merge([
                    'is_approved' => $topic->is_approved ? 'approved' : 'pending'
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Topics retrieved successfully',
                'data' => [
                    'topics' => $transformed,
                    'is_authenticated' => (bool)$userId,
                    'user_id' => $userId,
                    'pagination' => [
                        'total' => $topics->total(),
                        'per_page' => $topics->perPage(),
                        'current_page' => $topics->currentPage(),
                        'last_page' => $topics->lastPage(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteTopic($id)
    {
        try {
            $topic = FanTopic::where('id', $id)->where('user_id', auth()->id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Topic deleted successfully',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getMediaType($file): string
    {
        return str_contains($file->getMimeType(), 'video') ? 'video' : 'image';
    }

    private function generateVideoThumbnail($videoFile, $postId): ?string
    {
        try {
            // Ensure FFmpeg is installed
            if (!extension_loaded('ffmpeg')) {
                throw new \Exception('FFmpeg extension not loaded');
            }

            // Create storage paths
            $thumbnailPath = "fan_posts/{$postId}/thumbnails";
            $thumbnailName = 'thumbnail_' . time() . '.jpg';
            $fullThumbnailPath = "/var/www/footballnigeria/portal/public/images/post/{$thumbnailPath}";

            // Ensure directory exists
            if (!file_exists($fullThumbnailPath)) {
                mkdir($fullThumbnailPath, 0755, true);
            }

            // Temporary video path
            $tempVideoPath = $videoFile->getRealPath();

            // Initialize FFmpeg
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/bin/ffmpeg', // Path to ffmpeg binary
                'ffprobe.binaries' => '/usr/bin/ffprobe', // Path to ffprobe binary
                'timeout'          => 3600, // Timeout for processes
                'ffmpeg.threads'   => 12,   // Number of threads
            ]);

            // Open the video
            $video = $ffmpeg->open($tempVideoPath);

            // Get video duration to capture thumbnail from 10% of duration
            $duration = $video->getFFProbe()
                ->format($tempVideoPath)
                ->get('duration');
            $seconds = $duration * 0.1;

            // Capture frame and save thumbnail
            $video->frame(TimeCode::fromSeconds($seconds))
                ->save("{$fullThumbnailPath}/{$thumbnailName}");

            // Return the public accessible path
            return "/images/{$thumbnailPath}/{$thumbnailName}";
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
