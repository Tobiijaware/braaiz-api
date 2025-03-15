<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interfaces\IWalletService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Wallet",
 *     description="Wallet endpoints"
 * )
 */
class WalletController extends Controller
{
    private IWalletService $walletService;

    public function __construct(IWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * @OA\Post(
     *     path="/api/wallet/create",
     *     summary="Create a new wallet",
     *     description="Creates a wallet for the authenticated user with the specified currency.",
     *     operationId="createWallet",
     *     tags={"Wallet"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"currency"},
     *             @OA\Property(property="currency", type="string", example="USD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Wallet created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Wallet created successfully."),
     *             @OA\Property(property="wallet_id", type="integer", example=1234)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid currency or wallet already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid currency or wallet already exists.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function createWallet(Request $request)
    {
        $currency = $request->input('currency');
        $response = $this->walletService->createWallet($currency);
        return $response->toResponse();
    }

   /**
     * @OA\Get(
     *     path="/api/wallet/list",
     *     summary="List all wallets",
     *     description="Fetches a paginated list of wallets for the authenticated user.",
     *     operationId="listWallets",
     *     tags={"Wallet"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of wallets per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallets retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=50),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="currency", type="string", example="USD"),
     *                     @OA\Property(property="balance", type="number", format="float", example=1000.50),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function listWallets(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $response = $this->walletService->listWallets($perPage);
        return $response->toResponse();
    }

     /**
     * @OA\Get(
     *     path="/api/wallet/list/{walletId}",
     *     summary="View a single wallet",
     *     description="Fetches details of a specific wallet belonging to the authenticated user.",
     *     operationId="viewWallet",
     *     tags={"Wallet"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="walletId",
     *         in="path",
     *         description="ID of the wallet to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallet retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="currency", type="string", example="USD"),
     *             @OA\Property(property="balance", type="number", format="float", example=1000.50),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Wallet not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function viewWallet($walletId)
    {
        $response = $this->walletService->viewWallet($walletId);
        return $response->toResponse();
    }
}

