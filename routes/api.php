<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//user registration
Route::prefix('auth')->group(function () {
    Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login'); // login works for both admin and user.
    });
});


Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {
    Route::controller(\App\Http\Controllers\UserController::class)->group(function () {
        Route::get('/profile', 'getProfile')->middleware('role:user'); 
    });
});

Route::prefix('wallet')->middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::controller(\App\Http\Controllers\WalletController::class)->group(function () {
        Route::post('/create', 'createWallet');  // Create Wallet
        Route::get('/list', 'listWallets');    // List all wallets
        Route::get('/list/{walletId}', 'viewWallet'); // View a single wallet
    });
});

Route::prefix('wallet')->middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::controller(\App\Http\Controllers\TransactionController::class)->group(function () {
        Route::post('/transfer', 'transfer');  // transfer Wallet
    });
});

Route::prefix('transfer-request')->middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::controller(\App\Http\Controllers\TransferRequestController::class)->group(function () {
        Route::post('/request', 'requestMoney'); 
        Route::post('/accept-request/{id}', 'acceptRequest'); 
        Route::post('/reject-request/{id}', 'rejectRequest'); 
    });
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::controller(\App\Http\Controllers\AdminController::class)->group(function () {
        Route::get('/users', 'getUsers'); 
        Route::get('/user/{id}', 'getUser'); 
        Route::get('/transactions', 'getAllTransactions'); 
        Route::get('/transaction/{id}', 'getTransactionById'); 

        Route::delete('/exchange-rate/{id}', 'deleteExchangeRate'); 
        Route::put('/exchange-rate/{id}', 'updateExchangeRate');
        Route::post('/exchange-rate', 'createExchangeRate'); 
        Route::get('/exchange-rates', 'getAllExchangeRates'); 
    });
});

