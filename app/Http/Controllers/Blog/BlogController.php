<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Blog;
use Illuminate\Support\Facades\Auth;
use Exception;

class BlogController extends Controller
{
    public function create_blog(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'category_id' => 'required|integer',
                    'title' => 'required|string|max:255',
                    'content' => 'required|string'
                ]);

                // Generate slug from title
                $slug = \Str::slug($validated['title']);

                // Ensure slug is unique
                $originalSlug = $slug;
                $counter = 1;
                while (Blog::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $blog = Blog::create([
                    'category_id' => $validated['category_id'],
                    'user_id' => (Auth::user())->id,
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'slug' => $slug,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Blog created successfully',
                    'data' => $blog
                ], 201);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function get_blog(Request $request){
        try{
            $blogs = Blog::with(['category', 'comments', 'likes'])
                            ->get()
                            ->groupBy('category_id')
                            ->map(function ($group) {
                                return $group->values();
                            });

            return response()->json([
                'status' => 'success',
                'message' => 'Blogs fetched successfully',
                'data' => $blogs
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update_blog(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'id' => 'required|integer|exists:blogs,id',
                    'category_id' => 'required|integer',
                    'title' => 'required|string|max:255',
                    'content' => 'required|string'
                ]);

                $blog = Blog::findOrFail($validated['id']);

                if (!$blog) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Blog not found',
                    ], 404);
                }

                $blog->update([
                    'category_id' => $validated['category_id'],
                    'user_id' => (Auth::user())->id,
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Blog updated successfully',
                    'data' => $blog
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete_blog(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'id' => 'required|integer|exists:blogs,id',
                ]);

                $blog = Blog::findOrFail($validated['id']);

                if (!$blog) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Blog not found',
                    ], 404);
                }

                $blog->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Blog deleted successfully',
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
