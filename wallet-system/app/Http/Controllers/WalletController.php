<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;



class WalletController extends Controller
{
    // Create a new wallet
    public function create(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'currency' => 'required',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error occurred during wallet creation',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Create the wallet
            $wallet = Wallet::create([
                'user_id' => $request->user_id,
                'currency' => $request->currency,
                'balance' => 0, // Assuming a new wallet starts with a balance of 0
            ]);

            // Return success response
            return response()->json([
                'message' => 'Wallet successfully created',
                'wallet' => $wallet
            ], 201);

        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred during wallet creation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Credit the wallet with an amount using Paystack
    // Credit the wallet with an amount using Paystack
    // Credit the wallet with an amount using Paystack
    public function credit(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'wallet_id' => 'required|exists:wallets,id',
        'amount' => 'required|numeric|min:100', // Paystack minimum amount is 100 NGN
        'email' => 'required|email',
    ]);

    // Return validation errors if any
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error occurred during wallet credit initiation',
            'errors' => $validator->errors()
        ], 400);
    }

    try {
        // Initiate the Paystack payment request
        $paystackResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
        ])->post(env('PAYSTACK_PAYMENT_URL') . '/transaction/initialize', [
            'email' => $request->email,
            'amount' => $request->amount * 100, // Paystack expects the amount in kobo
            'callback_url' => route('wallet.callback'),
            'metadata' => [ // Send the wallet_id in the metadata
                'wallet_id' => $request->wallet_id,
            ],
        ]);

        $responseBody = $paystackResponse->json();

        if ($paystackResponse->failed() || !$responseBody['status']) {
            return response()->json([
                'message' => 'Paystack payment initialization failed',
                'error' => $responseBody['message'] ?? 'Unknown error'
            ], 400);
        }

        // Return the authorization URL for the user to make payment
        return response()->json([
            'message' => 'Payment initiated, redirect user to Paystack',
            'authorization_url' => $responseBody['data']['authorization_url'] ?? null
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An unexpected error occurred during payment initialization',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Handle Paystack callback
   // Handle Paystack callback
public function handlePaystackCallback(Request $request)
{
    $paymentReference = $request->query('reference');

    if (!$paymentReference) {
        return response()->json([
            'message' => 'Payment reference not found'
        ], 400);
    }

    try {
        // Verify the transaction with Paystack
        $paystackResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
        ])->get(env('PAYSTACK_PAYMENT_URL') . "/transaction/verify/{$paymentReference}");

        $responseBody = $paystackResponse->json();

        if ($paystackResponse->failed() || !$responseBody['status']) {
            return response()->json([
                'message' => 'Paystack verification failed',
                'error' => $responseBody['message'] ?? 'Unknown error'
            ], 400);
        }

        // Check if the transaction was successful
        $paymentData = $responseBody['data'] ?? [];

        if ($paymentData['status'] == 'success') {
            // Check if 'metadata' is an array and contains 'wallet_id'
            if (isset($paymentData['metadata']) && is_array($paymentData['metadata']) && isset($paymentData['metadata']['wallet_id'])) {
                // Find the wallet and credit it with the verified amount
                $wallet = Wallet::find($paymentData['metadata']['wallet_id']);
                
                if ($wallet) {
                    $wallet->balance += $paymentData['amount'] / 100; // Amount is in kobo
                    $wallet->save();

                    return response()->json([
                        'message' => 'Wallet successfully credited',
                        'wallet' => $wallet
                    ], 200);
                }

                return response()->json([
                    'message' => 'Wallet not found'
                ], 404);
            } else {
                return response()->json([
                    'message' => 'Metadata or wallet_id missing in Paystack response'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Transaction was not successful',
            'status' => $paymentData['status']
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An unexpected error occurred during transaction verification',
            'error' => $e->getMessage()
        ], 500);
    }
}

}