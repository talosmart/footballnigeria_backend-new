<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function user(Request $request)
    {
        try{
            $user = User::Where([Auth()->user()->id, $id])->get();

            return response()->json([
                'status' => "success",
                'data' =>$user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try{
            $user = User::FindOrFail(Auth()->user()->id);

            if(!$user){
                return response()->json([
                    'status' => "error",
                    'message' => "user not found"
                ]); 
            }

            $request->validate([
                'username' => ['sometimes', 'string', 'max:255', 'unique:fan_users,username,'.$user->id],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:fan_users,email,'.$user->id],
                'first_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['nullable', 'string', 'max:255'],
                'birthdate' => ['nullable', 'date'],
                'bio' => ['nullable', 'string', 'max:500'],
            ]);


            $user->update($request->only([
                'username', 'email', 'first_name', 'last_name', 'birthdate', 'bio'
            ]));

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        try{
            $request->validate([
                'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            $user = User::FindOrFail(Auth()->user()->id);

            if(!$user){
                return response()->json([
                    'status' => "error",
                    'message' => "user not found"
                ]); 
            }

            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');

            $user->update(['avatar' => $path]);

            return response()->json([
                'message' => 'Profile picture updated successfully',
                'avatar_url' => Storage::url($path)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        try{
            $user = User::FindOrFail(Auth()->user()->id);;

            if(!$user){
                return response()->json([
                    'status' => "error",
                    'message' => "user not found"
                ]); 
            }

            // Delete user's avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }

            // Revoke all tokens
            $user->tokens()->delete();

            // Delete user
            $user->delete();

            return response()->json([
                'message' => 'Account deleted successfully',
            ]);
         } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
