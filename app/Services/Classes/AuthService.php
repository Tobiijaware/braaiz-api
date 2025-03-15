<?php

namespace App\Services\Classes;

use App\Services\Interfaces\IAuthService;
use Illuminate\Support\Facades\Auth;
use App\Dtos\ApiResponseDTO;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Log;


class AuthService implements IAuthService
{
    public function register(array $data): ApiResponseDTO
    {
        try 
        {
            $validator = Validator::make($data, [
                'firstname' => 'required|string|max:50',
                'lastname' => 'required|string|max:50',
                'email' => 'required|string|email|max:50|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return (new ApiResponseDTO(false, null, $validator->errors()->first(), 422));
            }

            $user = User::create([
                'firstname' => sanitize_name($data['firstname']), //sanitize comes from App/Helper/helper.php to remove white spaces and special characters from names.
                'lastname' => sanitize_name($data['lastname']),
                'email' => strtolower(trim($data['email'])),
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('user');

            $token = $user->createToken('auth_token')->plainTextToken;

            return (new ApiResponseDTO(true, ['user' => $user, 'token' => $token], 'User registered successfully', 201));

        } 
        catch (\Exception $e)
        {
            Log::error('User registration failed: ' . $e->getMessage());
            return (new ApiResponseDTO(false, null, 'An error occurred while registering the user', 500));
        }
    }

    public function login(array $credentials) : ApiResponseDTO
    {
        try
        {
            $validator = Validator::make($credentials, [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return (new ApiResponseDTO(false, null, $validator->errors()->first(), 422));
            }
    
            $user = User::where('email', strtolower($credentials['email']))->first();
    
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return (new ApiResponseDTO(false, null, 'Invalid credentials', 401));
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return (new ApiResponseDTO(true, ['user' => $user, 'token' => $token], 'Login successful'));

        }
        catch (\Exception $e)
        {
            Log::error('User login failed: ' . $e->getMessage());
            return (new ApiResponseDTO(false, null, 'An error occurred during login', 500));
        }
    }
}
