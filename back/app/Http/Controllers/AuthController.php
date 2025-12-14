<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        // everything else comes from DB defaults
    ]);

    return response()->json([
        'user' => $user,
        'token' => $user->createToken('auth')->plainTextToken,
    ], 201);
}


        try {
            $user = User::create([
                'name'  => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),

                // 🔐 EXPLICIT DEFAULTS (OPTION 2)
                'role' => 'user',
                'verified' => false,
                'profile_completion' => 0,
                'verification_status' => 'unverified',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (\Throwable $e) {
            // 👀 CRITICAL for Render debugging
            \Log::error('REGISTER FAILED', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Registration failed',
            ], 500);
        }
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
