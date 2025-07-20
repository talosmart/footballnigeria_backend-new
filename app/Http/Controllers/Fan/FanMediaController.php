<?php

namespace App\Http\Controllers\Fan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FanPost;
use App\Models\FanMedia;

class FanMediaController extends Controller
{
    public function addMedia(Request $request, FanPost $post)
    {
        try{
            $request->validate([
                'media' => 'required|file|mimes:jpg,png,mp4,gif|max:10240',
            ]);

            $file = $request->file('media');
            $path = \Storage::putFile("fan_posts/{$post->id}/media", $file);

            $media = FanMedia::create([
                'post_id' => $post->id,
                'type' => $this->getMediaType($file),
                'url' => $path,
                'thumbnail_url' => $this->generateThumbnailIfVideo($file),
                'order' => $post->media()->count(),
            ]);

            return response()->json([
                'status' => 'success',
                'media' => $media,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
