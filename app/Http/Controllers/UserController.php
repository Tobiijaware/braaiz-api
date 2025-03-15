<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interfaces\IUserService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="User",
 *     description="User endpoints"
 * )
 */
class UserController extends Controller
{
    protected IUserService $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     summary="Get User Profile with Wallets",
     *     tags={"User"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="firstname", type="string", example="John"),
     *                     @OA\Property(property="lastname", type="string", example="Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com")
     *                 ),
     *                 @OA\Property(property="wallets", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="currency", type="string", example="USD"),
     *                         @OA\Property(property="balance", type="number", example=100.50)
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="User profile retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function getProfile()
    {
        // Get authenticated user
        $user = auth()->user();
        $response = $this->userService->profile($user);
        return $response->toResponse();
    }
}
