<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\TransactionApprovalNotification;
use Illuminate\Support\Facades\Notification;

class TransactionController extends Controller
{
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $fromWallet = Wallet::find($request->from_wallet_id);
        $toWallet = Wallet::find($request->to_wallet_id);

        if ($fromWallet->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient funds'], 400);
        }

        // Create the transaction and set it as not approved if amount > 1,000,000
        $transaction = Transaction::create([
            'from_wallet_id' => $request->from_wallet_id,
            'to_wallet_id' => $request->to_wallet_id,
            'amount' => $request->amount,
            'approved' => false, // Not approved initially
        ]);

        // If the transaction is greater than 1,000,000, require admin approval
        if ($request->amount > 1000000) {
            // Notify the admin for approval (you could send a notification if necessary)
            // $admin = User::where('is_admin', true)->first();
            // Notification::send($admin, new TransactionApprovalNotification($transaction));

            return response()->json([
                'message' => 'Transaction submitted for approval, waiting for admin approval',
                'transaction_id' => $transaction->id // Include the transaction ID in the response
            ], 202);
        }

        // Proceed with the transaction if below the threshold
        $fromWallet->balance -= $request->amount;
        $toWallet->balance += $request->amount;
        $fromWallet->save();
        $toWallet->save();

        $transaction->approved = true;
        $transaction->save();

        return response()->json(['transaction' => $transaction], 201);
    }

    // Admin approval of a pending transaction
    public function approveTransaction(Request $request, $id)
    {
        // Find the transaction by ID
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->approved) {
            return response()->json(['message' => 'Transaction already approved'], 400);
        }

        // Approve the transaction
        $transaction->approved = true;
        $transaction->save();

        // Adjust wallet balances
        $fromWallet = Wallet::find($transaction->from_wallet_id);
        $toWallet = Wallet::find($transaction->to_wallet_id);

        if ($fromWallet && $toWallet) {
            $fromWallet->balance -= $transaction->amount;
            $toWallet->balance += $transaction->amount;
            $fromWallet->save();
            $toWallet->save();
        }

        return response()->json(['message' => 'Transaction approved successfully'], 200);
    }
}


