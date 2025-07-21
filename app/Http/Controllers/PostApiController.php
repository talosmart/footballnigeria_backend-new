<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;

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
