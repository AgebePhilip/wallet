<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // User Registration
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error occurred during registration',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Create a new user
            $user = User::create([
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);

            // Generate a personal access token
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            // Return success response
            return response()->json([
                'message' => 'User successfully created',
                'phone_number' => $user->phone_number,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred during registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // User Login
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error occurred during login',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Find the user by phone number
            $user = User::where('phone_number', $request->phone_number)->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Generate a personal access token
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            // Return success response
            return response()->json([
                'message' => 'User successfully signed in',
                'phone_number' => $user->phone_number,
                'token' => $token
            ], 200);

        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
