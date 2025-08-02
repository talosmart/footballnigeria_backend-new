<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Tags\Tag;

class TagController extends Controller
{
    public function createTag(Request $request){
        try{
            $data = $request->all();

            foreach($data['name'] as $tagName){
                Tag::findOrCreateFromString($tagName);
            }

            return response()->json(["message" => "Tag successfully saved"]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getTag(Request $request){
        try{
            $tag = Tag::all();

            return response()->json([
                "message" => "Success",
                "data" => $tag
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateTag(Request $request, $tagName){
        try{
            $data = $request->all();

            $tag = Tag::findFromString($tagName);

            $tag->name = $data['name'];

            $tag->save();

            return response()->json(["message" => "Tag successfully edited"]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteTag($tagName){
        try{
            $tag = Tag::findFromString($tagName);

            $tag->delete();

            return response()->json([
                "message" => "Successfully deleted"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
