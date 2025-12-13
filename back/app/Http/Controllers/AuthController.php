<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // 1️⃣ Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2️⃣ Create user with REQUIRED DEFAULTS
        $user = User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),

            // 🔹 REQUIRED FIELDS (IMPORTANT)
            'role' => 'user',
            'verified' => false,
            'verification_status' => 'unverified',

            // 🔹 SAFE DEFAULTS
            'rating' => 0,
            'total_ratings' => 0,
            'profile_completion' => 0,
            'is_online' => false,
            'show_email' => true,
            'show_contact' => true,
            'show_social_link' => true,
        ]);

        // 3️⃣ Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4️⃣ Response
        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        // 1️⃣ Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2️⃣ Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // 3️⃣ Check account status
        if ($user->verification_status === 'inactive') {
            return response()->json([
                'error' => 'Account deactivated',
                'message' => 'Your account has been deactivated. Please contact support.'
            ], 403);
        }

        // 4️⃣ Update online status
        $user->setOnline(true);

        // 5️⃣ Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $request->user()->setOnline(false);

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
