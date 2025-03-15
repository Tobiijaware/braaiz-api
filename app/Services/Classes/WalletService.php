<?php

namespace App\Services\Classes;

use App\Services\Interfaces\IWalletService;
use App\Dtos\ApiResponseDTO;
use App\Models\Wallet;
use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Enums\CurrencyEnum;

class WalletService implements IWalletService
{
    public function createWallet($currency): ApiResponseDTO
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return new ApiResponseDTO(false, null, 'Unauthorized', 401);
            }

            // Validate if currency is in our defined ENUM
            // if (!CurrencyEnum::tryFrom($currency)) {
            //     return new ApiResponseDTO(false, null, 'Invalid currency type', 400);
            // }

            $currencyExists = CurrencyRate::where('from_currency', $currency)->exists(); 
            if (!$currencyExists) {
                return new ApiResponseDTO(false, null, 'Invalid currency type', 400);
            }

            // Check if the user already has a wallet with this currency
            if ($user->wallets()->where('currency', $currency)->exists()) {
                return new ApiResponseDTO(false, null, 'Wallet with this currency already exists', 400);
            }

            $wallet = Wallet::create([
                'user_id'  => $user->id,
                'currency' => $currency,
                'balance'  => 500.00 //credit with 500
            ]);

            return new ApiResponseDTO(true, $wallet, 'Wallet created successfully', 201);

        } catch (\Exception $e) {
            Log::error('Failed to create wallet: ' . $e->getMessage());
            return new ApiResponseDTO(false, null, 'An error occurred while creating the wallet', 500);
        }
    }

    public function listWallets($perPage = 10): ApiResponseDTO
    {
        try {
            $user = Auth::user();
    
            if (!$user) {
                return new ApiResponseDTO(false, null, 'Unauthorized', 401);
            }
    
            $wallets = $user->wallets()->paginate($perPage);
    
            return new ApiResponseDTO(true, $wallets, 'Wallets retrieved successfully', 200);
    
        } catch (\Exception $e) {
            Log::error('Failed to fetch wallets: ' . $e->getMessage());
            return new ApiResponseDTO(false, null, 'An error occurred while fetching wallets', 500);
        }
    }

    public function viewWallet($walletId): ApiResponseDTO
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return new ApiResponseDTO(false, null, 'Unauthorized', 401);
            }

            $wallet = $user->wallets()->where('id', $walletId)->first();

            if (!$wallet) {
                return new ApiResponseDTO(false, null, 'Wallet not found', 404);
            }

            return new ApiResponseDTO(true, $wallet, 'Wallet retrieved successfully', 200);

        } catch (\Exception $e) {
            Log::error('Failed to fetch wallet: ' . $e->getMessage());
            return new ApiResponseDTO(false, null, 'An error occurred while fetching the wallet', 500);
        }
    }
}
