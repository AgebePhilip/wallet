<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Wallet routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wallets/create', [WalletController::class, 'create']);
    Route::post('/wallets/credit', [WalletController::class, 'credit']);
    
    Route::post('/transactions/transfer', [TransactionController::class, 'transfer']);
});


//paystack call back
Route::get('/wallet/callback', [WalletController::class, 'handlePaystackCallback'])->name('wallet.callback');

// Admin routes
Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminController::class, 'register']);
    Route::post('/login', [AdminController::class, 'login']);
    Route::get('/monthly-summary', [AdminController::class, 'monthlySummary']);
    
    //route for approving transactions
    Route::post('/transactions/{id}/approve', [AdminController::class, 'approveTransaction'])->middleware('auth:sanctum');
});

//get users 
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
