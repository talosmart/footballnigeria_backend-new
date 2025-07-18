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
            $validator = \Validator::make($request->all(), [
                'paginate' => 'required|integer|min:1|max:100', 
            ]);
        
            if ($validator->fails()) {
                return laraResponse('Validation error', [
                    'errors' => $validator->errors(),
                ])->error();
            }
            
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


    public function singlePost(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'id' => 'required|integer', // Validate pagination
            ]);
        
            if ($validator->fails()) {
                return laraResponse('Validation error', [
                    'errors' => $validator->errors(),
                ])->error();
            }
            
            $posts = Post::query()->with(['author','seo'])->find($request->id);
            
            return response()->json([
                'status' => 'error',
                'data' => new PostResource( $posts), 
            ])->success();
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
