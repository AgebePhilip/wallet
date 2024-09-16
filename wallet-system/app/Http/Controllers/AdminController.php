<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|unique:admins',
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
            // Create a new admin
            $admin = Admin::create([
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);

            // Generate a personal access token
            $token = $admin->createToken('Personal Access Token')->plainTextToken;

            // Return success response with token
            return response()->json([
                'message' => 'Admin successfully created',
                'phone_number' => $admin->phone_number,
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

    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find admin by phone number
        $admin = Admin::where('phone_number', $request->phone_number)->first();

        // Check if admin exists and password is correct
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate a personal access token
        $token = $admin->createToken('Personal Access Token')->plainTextToken;

        // Return the token
        return response()->json([
            'message' => 'Admin successfully created',
            'phone_number' => $admin->phone_number,
            'token' => $token
        ], 200);
    }

    public function monthlySummary(Request $request)
    {
        // Get all transactions for the current month
        $transactions = Transaction::whereMonth('created_at', date('m'))->get();
        $totalAmount = $transactions->sum('amount');

        // Return the summary
        return response()->json([
            'total_amount' => $totalAmount,
            'transactions' => $transactions
        ], 200);
    }

    // Approve transaction (you may require authentication middleware for this route)
    public function approveTransaction(Request $request, $id)
    {
        // Approve the transaction logic will be handled in TransactionController
        return (new TransactionController())->approveTransaction($request, $id);
    }
}
