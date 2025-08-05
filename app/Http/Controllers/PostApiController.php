<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\SeoData;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use Illuminate\Support\Str;

class PostApiController extends Controller
{
    public function category(){
        try{
            $data = CategoryResource::collection(Category::all());

            return response()->json([
                'status' => 'success',
                'data'  => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createPostCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:fan_categories,slug',
                'icon' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:500',
                'description' => 'required|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'head1' => 'nullable|string|max:255',
                'head2' => 'nullable|string|max:255',
                'summary' => 'nullable|string|max:1000',
                'content' => 'nullable|string',
            ]);

            $category = Category::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function createPost(Request $request)
    {
        try{
            $validator = \Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category_id' => 'required|exists:fan_categories,id',
                'excerpt' => 'nullable|string',
                'featured_image' => 'required|file',
                'published_at' => 'nullable|date',
                'is_featured_video' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',
                'is_trending' => 'nullable|boolean',
                'tags' => 'nullable|array',
                // SEO fields
                'model_type' => 'nullable|string|max:255',
                'model_id' => 'nullable|integer',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string|max:500',
                'og_image' => 'nullable|file',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string|max:500',
                'twitter_image' => 'nullable|file',
                'structured_data' => 'nullable|string',
                'seoable_id' => 'nullable|integer',
                'seoable_type' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }

            $file = localUploadFile($request->featured_image);

            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'is_featured' => $request->is_featured,
                'is_trending' => $request->is_trending,
                'slug' => $this->generateUniqueSlug($request->title),
                'excerpt' => $request->excerpt,
                'featured_image' => $file['filePath'],
                'published_at' => \Carbon\Carbon::now(),
                'is_featured_video' => $file['mimeType'] === 'video' ? true : false,
                'author_id' => auth()->id(),
            ]);

            $file = localUploadFile($request->og_image);

            $twiter = localUploadFile($request->twitter_image);

            $seoData = new SeoData([
                'model_type' => Post::class,
                'model_id' => $post->id,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'og_title' => $request->og_title,
                'og_description' => $request->og_description,
                'og_image' => $file['filePath'],
                'twitter_title' => $request->twitter_title,
                'twitter_description' => $request->twitter_description,
                'twitter_image' => $twiter['filePath'],
                'structured_data' => $request->structured_data,
                'seoable_id' => $post->id,
                'seoable_type' => Post::class,
            ]);

            $post->seo()->save($seoData);

            $tags = $request->tags;

            $post->attachTags($tags);

            return response()->json([
                'status' => 'success',
                'data' => new PostResource($post),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    } 
    
    public function listPostBytag($tag)
    {
        try{
            $tags = explode(',', urldecode($tag));

            $posts = Post::with(['author','seo'])->withAnyTags($tags)->get();

            return response()->json([
                'status' => 'success',
                'data' => PostResource::collection($posts)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function listPost(Request $request)
    {
        try{
            $query = Post::query()->with(['author','seo']);
        
            if ($request->filled('category_id')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('id', $request->category_id);
                });
            }
    
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                    ->orWhere('content', 'like', '%' . $request->search . '%');
                });
            }
        
            $posts = $query->latest('created_at')->paginate(request()->paginate); 

            return response()->json([
                'status' => 'success',
                'data' => PostResource::collection( $posts->items()), // The posts data
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'last_page' => $posts->lastPage(),
                    'next_page_url' => $posts->nextPageUrl(),
                    'prev_page_url' => $posts->previousPageUrl(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function singlePost($id){
        try{
            $validator = \Validator::make(['id' => $id], [
                'id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }
            
            $check = Post::find($id);
            if (!$check) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post not found',
                ], 404);
            }

            $posts = Post::query()->with(['author','seo'])->find($id);
            
            return response()->json([
                'status' => 'error',
                'data' => new PostResource( $posts), 
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
