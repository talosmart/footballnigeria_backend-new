<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Category;
use Exception;

class CategoryController extends Controller
{
    public function create_category(Request $request){
        try{
            // if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'name' => 'required|string|max:255'
                ]);

                $category = Category::create([
                    'name' => $validated['name']
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Category created successfully',
                    'data' => $category
                ], 201);
            // }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function get_category(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $category = Category::get();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Categories fetched successfully',
                    'data' => $category
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update_category(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'id' => 'required|integer|exists:categories,id',
                    'name' => 'required|string|max:255'
                ]);

                $category = Category::findOrFail($validated['id']);

                if (!$category) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Category not found',
                    ], 404);
                }

                $category->update([
                    'name' => $validated['name']
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Category updated successfully',
                    'data' => $category
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete_category(Request $request){
        try{
            if (auth()->user()->role === 'admin') {
                $validated = $request->validate([
                    'id' => 'required|integer|exists:categories,id',
                ]);

                $category = Category::findOrFail($validated['id']);

                if (!$category) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Category not found',
                    ], 404);
                }

                $category->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Category deleted successfully',
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
