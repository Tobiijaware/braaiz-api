<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\ITransactionService;
use App\Services\Interfaces\IExchangeRateService;


 /**
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin endpoints"
 * )
 */
class AdminController extends Controller
{
    protected IUserService $userService;
    protected ITransactionService $transactionService;
    protected IExchangeRateService $exchangeRateService;

    public function __construct(IUserService $userService, ITransactionService $transactionService, IExchangeRateService $exchangeRateService)
    {
        $this->userService = $userService;
        $this->transactionService = $transactionService;
        $this->exchangeRateService = $exchangeRateService;
    }

    
    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="View all users",
     *     description="Retrieve a list of all users, filterable by name, email, and creation date.",
     *     tags={"Admin"},
     *     @OA\Parameter(
     *         name="firstname",
     *         in="query",
     *         description="Filter by firstname",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="lastname",
     *         in="query",
     *         description="Filter by lastname",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter by email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="created_at",
     *         in="query",
     *         description="Filter by creation date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="firstname", type="string", example="John"),
     *                     @OA\Property(property="lastname", type="string", example="Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Users list retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getUsers(Request $request)
    {
        $response = $this->userService->getAllUsers($request->all());
        return $response->toResponse();
    }

    /**
     * @OA\Get(
     *     path="/api/admin/user/{id}",
     *     summary="View single user profile",
     *     description="Retrieve a user's profile including their wallets and recent transactions.",
     *     tags={"Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
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
     *                 ),
     *                 @OA\Property(property="transactions", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="transaction_id", type="integer", example=123),
     *                         @OA\Property(property="amount", type="number", example=50.75),
     *                         @OA\Property(property="status", type="string", example="Completed"),
     *                         @OA\Property(property="date", type="string", format="date-time", example="2025-03-15T14:00:00Z")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="User profile retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function getUser($id)
    {
        $response = $this->userService->getUserById($id);
        return $response->toResponse();
    }

    /**
     * @OA\Get(
     *     path="/api/admin/transactions",
     *     summary="Get all transactions",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user_id", in="query", description="Filter by user ID", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="currency", in="query", description="Filter by currency", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="date_from", in="query", description="Filter from date (YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_to", in="query", description="Filter to date (YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="transaction_id", type="integer", example=123),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="amount", type="number", example=150.75),
     *                     @OA\Property(property="currency", type="string", example="USD"),
     *                     @OA\Property(property="status", type="string", example="Completed"),
     *                     @OA\Property(property="date", type="string", format="date-time", example="2025-03-15T14:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Transactions retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function getAllTransactions(Request $request)
    {
        $response = $this->transactionService->getAllTransactions($request->all());
        return $response->toResponse();
    }

    /**
     * @OA\Get(
     *     path="/api/admin/transaction/{id}",
     *     summary="Get transaction by ID",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Transaction ID", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transaction_id", type="integer", example=123),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", example=150.75),
     *                 @OA\Property(property="currency", type="string", example="USD"),
     *                 @OA\Property(property="status", type="string", example="Completed"),
     *                 @OA\Property(property="date", type="string", format="date-time", example="2025-03-15T14:00:00Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Transaction retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Transaction not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function getTransactionById(int $id)
    {
        $response = $this->transactionService->getTransactionById($id);
        return $response->toResponse();
    }

     /**
     * @OA\Get(
     *     path="/api/admin/exchange-rates",
     *     summary="Get all exchange rates",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="currency", type="string", example="USD"),
     *                     @OA\Property(property="rate", type="number", example=1.2345),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-15T14:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Exchange rates retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function getAllExchangeRates()
    {
        $response = $this->exchangeRateService->getAllRates();
        return $response->toResponse();
    }

    /**
     * @OA\Post(
     *     path="/api/admin/exchange-rate",
     *     summary="Create a new exchange rate",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_currency", "to_currency", "rate"},
     *             @OA\Property(property="from_currency", type="string", example="USD"),
     *             @OA\Property(property="to_currency", type="string", example="EUR"),
     *             @OA\Property(property="rate", type="number", format="float", example=1.02)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Exchange rate created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="from_currency", type="string", example="USD"),
     *                 @OA\Property(property="to_currency", type="string", example="EUR"),
     *                 @OA\Property(property="rate", type="number", format="float", example=1.02),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-15T14:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-15T14:00:00Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Exchange rate created successfully")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function createExchangeRate(Request $request)
    {
        $response = $this->exchangeRateService->createRate($request->all());
        return $response->toResponse();
    }

    /**
     * @OA\Put(
     *     path="/api/admin/exchange-rate/{id}",
     *     summary="Update an exchange rate",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id", 
     *         in="path", 
     *         required=true, 
     *         description="Exchange rate ID", 
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rate"},
     *             @OA\Property(property="rate", type="number", format="float", example=1.02)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exchange rate updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="from_currency", type="string", example="USD"),
     *                 @OA\Property(property="to_currency", type="string", example="EUR"),
     *                 @OA\Property(property="rate", type="number", format="float", example=1.02),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-15T14:00:00Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Exchange rate updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Exchange rate not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function updateExchangeRate(int $id, Request $request)
    {
        $response = $this->exchangeRateService->updateRate($id, $request->all());
        return $response->toResponse();
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/exchange-rate/{id}",
     *     summary="Delete an exchange rate",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id", 
     *         in="path", 
     *         required=true, 
     *         description="Exchange rate ID", 
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exchange rate deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Exchange rate deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Exchange rate not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function deleteExchangeRate(int $id)
    {
        $response = $this->exchangeRateService->deleteRate($id);
        return $response->toResponse();
    }
}
