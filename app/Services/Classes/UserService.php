<?php

namespace App\Services\Classes;

use App\Services\Interfaces\IUserService;
use App\Dtos\ApiResponseDTO;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService implements IUserService
{
    public function profile($user): ApiResponseDTO
    {
        try {
            
            if (!$user) {
                return new ApiResponseDTO(false, null, 'Unauthorized', 401);
            }

            // Load user's wallets
            $user->load('wallets');
    
            return new ApiResponseDTO(true, ['user' => $user], 'Profile retrieved successfully', 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user profile: ' . $e->getMessage());
            return new ApiResponseDTO(false, null, 'An error occurred while fetching the profile', 500);
        }
    }

    public function getAllUsers(array $filters) : ApiResponseDTO
    {
        try {
            //$query = User::query();

            $query = User::query()->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'admin'); // Exclude users with the 'admin' role
            });

            if (!empty($filters['firstname'])) {
                $query->where('firstname', 'LIKE', '%' . $filters['firstname'] . '%');
            }

            if (!empty($filters['lastname'])) {
                $query->where('lastname', 'LIKE', '%' . $filters['lastname'] . '%');
            }

            if (!empty($filters['email'])) {
                $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
            }

            if (!empty($filters['created_at'])) {
                $query->whereDate('created_at', $filters['created_at']);
            }

            $result = $query->with('wallets')->paginate(10);

            return new ApiResponseDTO(true, ['users' => $result], 'Users retrieved successfully');


        } catch (\Exception $e) {
            Log::error('Error fetching users', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to retrieve all users', null, 500);
        }
    }

    public function getUserById(int $userId): ApiResponseDTO
    {
        try {
            $user = User::with([
                'wallets.sentTransactions' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'wallets.receivedTransactions' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])->find($userId);

            if (!$user) {
                return new ApiResponseDTO(false, 'User not found', null, 404);
            }

            return new ApiResponseDTO(true, ['user' => $user], 'User retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error fetching user details', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to retrieve user', null, 500);
        }
    }

}
