<?php

namespace App\Services\Interfaces;

use App\Dtos\ApiResponseDTO;

interface IUserService
{
    public function profile($user): ApiResponseDTO;
    public function getAllUsers(array $filters) : ApiResponseDTO;
    public function getUserById(int $userId): ApiResponseDTO;
}
