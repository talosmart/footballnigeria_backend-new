<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try{
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'phone_number' => 'required|string|max:15|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if (!filter_var($validated['email'], FILTER_VALIDATE_EMAIL)) {
                return response()->json(['message' => 'Invalid email format'], 422);
            }
            if (!preg_match('/^\+?[0-9]{10,15}$/', $request->phone_number)) {
                return response()->json(['message' => 'Invalid phone number format'], 422);
            }
            if (strlen($validated['phone_number']) < 8) {
                return response()->json(['message' => 'Password must be at least 8 characters'], 422);
            }
            if ($validated['password'] !== $request->password_confirmation) {
                return response()->json(['message' => 'Password confirmation does not match'], 422);
            }

            $user = User::create([
                'full_name' => $validated['full_name'],
                'country' => $validated['country'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'password' => \Hash::make($validated['password']),
            ]);

            if (! $user) {
                return response()->json(['message' => 'User registration failed'], 500);
            }

            $user->sendEmailVerificationNotification();

            $token = $user->createToken('api-token')->plainTextToken;

            event(new Registered($user));

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function login(Request $request)
    {
        try{
            $user = User::where('email', $request->email)->first();

            if (! $user || ! \Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try{
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function refresh(Request $request)
    {
        try{
            $user = $request->user();

            $request->user()->currentAccessToken()->delete();

            $newToken = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function forgot_password(Request $request)
    {
        try{
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return response()->json(['message' => __($status)]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reset_password(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            if ($request->password !== $request->password_confirmation) {
                return response()->json(['message' => 'Password confirmation does not match'], 422);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();

                    event(new \Illuminate\Auth\Events\PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Password reset successful.']);
            }

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
