<?php

namespace App\Services\Interfaces;

use App\Dtos\ApiResponseDTO;

interface IExchangeRateService
{
    public function getAllRates(): ApiResponseDTO;
    public function createRate(array $data): ApiResponseDTO;
    public function updateRate(int $id, array $data): ApiResponseDTO;
    public function deleteRate(int $id): ApiResponseDTO;
}
