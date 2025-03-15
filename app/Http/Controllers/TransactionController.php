<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interfaces\ITransactionService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Transaction",
 *     description="Transaction endpoints"
 * )
 */
class TransactionController extends Controller
{
    private ITransactionService $transactionService;

    public function __construct(ITransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }


    /**
     * @OA\Post(
     *     path="/api/wallet/transfer",
     *     summary="Transfer funds between wallets",
     *     description="Transfers funds from one user's wallet to another, converting currency if needed.",
     *     operationId="transferFunds",
     *     tags={"Transaction"},
     *     security={{"sanctum": {}}}, 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"sender_wallet_id", "receiver_wallet_id", "amount", "from_currency", "to_currency"},
     *             @OA\Property(property="sender_wallet_id", type="integer", example=1, description="ID of the sender's wallet"),
     *             @OA\Property(property="receiver_wallet_id", type="integer", example=2, description="ID of the receiver's wallet"),
     *             @OA\Property(property="amount", type="number", format="float", example=100.50, description="Amount to transfer"),
     *             @OA\Property(property="from_currency", type="string", example="USD", description="Currency of the sender's wallet"),
     *             @OA\Property(property="to_currency", type="string", example="EUR", description="Currency of the receiver's wallet"),
     *             @OA\Property(property="description", type="string", example="Payment for services", description="Optional transaction description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transfer successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transfer successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transaction_reference", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="sender_wallet_id", type="integer", example=1),
     *                 @OA\Property(property="receiver_wallet_id", type="integer", example=2),
     *                 @OA\Property(property="amount", type="number", format="float", example=100.50),
     *                 @OA\Property(property="converted_amount", type="number", format="float", example=95.75),
     *                 @OA\Property(property="currency", type="string", example="USD"),
     *                 @OA\Property(property="to_currency", type="string", example="EUR"),
     *                 @OA\Property(property="exchange_rate", type="number", format="float", example=0.9575),
     *                 @OA\Property(property="transaction_fee", type="number", format="float", example=0.00),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="description", type="string", example="Payment for services")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request or insufficient balance"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function transfer(Request $request)
    {
        $response = $this->transactionService->transfer($request->all());
        return $response->toResponse();
        
    }
}
