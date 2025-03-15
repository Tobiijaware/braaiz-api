<?php

namespace App\Services\Interfaces;

use App\Dtos\ApiResponseDTO;

interface IWalletService
{
    public function createWallet($currency): ApiResponseDTO;
    public function listWallets($perPage): ApiResponseDTO;
    public function viewWallet($walletId): ApiResponseDTO;
}
