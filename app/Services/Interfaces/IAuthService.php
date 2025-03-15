<?php

namespace App\Services\Interfaces;

use App\Dtos\ApiResponseDTO;

interface IAuthService
{
    public function register(array $data): ApiResponseDTO;
    public function login(array $credentials): ApiResponseDTO;
}
