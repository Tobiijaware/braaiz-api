<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ITransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;


 /**
 * @OA\Tag(
 *     name="Transfer Requests",
 *     description="Transfer Requests endpoints"
 * )
 */
class TransferRequestController extends Controller
{
    private ITransactionService $transactionService;

    public function __construct(ITransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @OA\Post(
     *     path="/api/transfer-request/request",
     *     summary="Request money from another user",
     *     tags={"Transfer Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient_id", "request_wallet_id", "amount", "currency"},
     *             @OA\Property(property="recipient_id", type="integer", example=5),
     *             @OA\Property(property="request_wallet_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=50.00),
     *             @OA\Property(property="currency", type="string", example="USD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request placed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transfer request placed successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function requestMoney(Request $request)
    {
        $response = $this->transactionService->requestMoney($request->all());
        return $response->toResponse();
    }

    /**
     * @OA\Post(
     *     path="/api/transfer-request/accept-request/{id}",
     *     summary="Accept a money request",
     *     tags={"Transfer Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Request accepted and money transferred successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Money request accepted successfully. Funds have been transferred.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Request not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function acceptRequest($id)
    {
        $response = $this->transactionService->acceptRequest($id);
        return $response->toResponse();
    }

   /**
     * @OA\Post(
     *     path="/api/transfer-request/reject-request/{id}",
     *     summary="Reject a money request",
     *     tags={"Transfer Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Request rejected successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Money request rejected successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Request not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function rejectRequest($id)
    {
        $response = $this->transactionService->rejectRequest($id);
        return $response->toResponse();
    }

}
