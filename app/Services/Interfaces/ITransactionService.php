<?php

namespace App\Services\Interfaces;

use App\Dtos\ApiResponseDTO;

interface ITransactionService
{
    public function transfer(array $data): ApiResponseDTO;

    public function requestMoney(array $data): ApiResponseDTO;
    public function acceptRequest($id): ApiResponseDTO;
    public function rejectRequest($id): ApiResponseDTO;

    public function getTransactionById(int $transactionId): ApiResponseDTO;
    public function getAllTransactions(array $filters): ApiResponseDTO;
    
}
