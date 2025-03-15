<?php

namespace App\Services\Classes;

use App\Services\Interfaces\ITransactionService;
use App\Dtos\ApiResponseDTO;
use App\Models\User;
use App\Models\Wallet;
use App\Models\CurrencyRate;
use App\Models\Transaction;
use App\Models\TransferRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class TransactionService implements ITransactionService
{
    /**
     * transfer
     */
    public function transfer(array $data): ApiResponseDTO
    {
        // Validate input data
        $validator = Validator::make($data, [
            'sender_wallet_id' => 'required|integer|exists:wallets,id',
            'receiver_wallet_id' => 'required|integer|exists:wallets,id|different:sender_wallet_id', //this prevents one wallet trying to send money to the same wallet
            'amount' => 'required|numeric|min:0.01',
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3',
            'description' => 'nullable|string|max:50',
        ]);

        $amount = $data['amount'];

        if ($validator->fails()) {
            return new ApiResponseDTO(false, 'Validation failed', $validator->errors(), 422);
        }

        DB::beginTransaction();

        try 
        {
            // Lock sender and receiver wallets for concurrency handling
            $senderWallet = Wallet::where('id', $data['sender_wallet_id'])->lockForUpdate()->first();
            $receiverWallet = Wallet::where('id', $data['receiver_wallet_id'])->lockForUpdate()->first();

            //validator already doing the commented check.
            //check that both wallets are valid
            // if (!$senderWallet || !$receiverWallet) {
            //     return new ApiResponseDTO(false, 'Wallet not found', null);
            // }

            //check for balance
            if ($senderWallet->balance < $amount) {
                return new ApiResponseDTO(false, 'Insufficient balance', null);
            }

            // Fetch currency exchange rate
            $exchangeRate = CurrencyRate::where('from_currency', $data['from_currency'])
            ->where('to_currency', $data['to_currency'])
            ->value('rate');

            if (!$exchangeRate) {
                return new ApiResponseDTO(false, 'Exchange rate not found', null, 400);
            }

            $convertedAmount = $amount * $exchangeRate;

            // Deduct from sender
            $senderWallet->balance -= $amount;
            $senderWallet->save();

            // Credit to receiver after calculating exchange rate
            $receiverWallet->balance += $convertedAmount;
            $receiverWallet->save();

            // Create transaction record
            $transaction = Transaction::create([
                'transaction_reference' => Str::uuid(),
                'sender_wallet_id' => $senderWallet->id,
                'receiver_wallet_id' => $receiverWallet->id,
                'amount' => $amount,
                'converted_amount' => $convertedAmount,
                'currency' => $data['from_currency'],
                'to_currency' => $data['to_currency'],
                'exchange_rate' => $exchangeRate,
                'status' => 'completed',
                'description' => $data['description']
            ]);
            
            DB::commit();

            //log details of the successful transaction
            // Log transaction details
            Log::info('Transaction Successful', [
                'transaction_reference' => $transaction->transaction_reference,
                'sender_wallet_id' => $senderWallet->id,
                'receiver_wallet_id' => $receiverWallet->id,
                'amount' => $amount,
                'converted_amount' => $convertedAmount,
                'currency' => $data['from_currency'],
                'to_currency' => $data['to_currency'],
                'exchange_rate' => $exchangeRate,
                'status' => 'completed',
                'description' => $data['description']
            ]);

            return new ApiResponseDTO(true, 'Transfer successful', $transaction);
            
        } catch (\Exception $e) {
            DB::rollBack();

            //Log any error
            Log::error('Transaction failed', ['error' => $e->getMessage()]);

            return new ApiResponseDTO(false, 'Transfer failed', null, 500);
        }
    }

    /**
     * request money.
     */
    public function requestMoney(array $data): ApiResponseDTO
    {
        $validated = Validator::make($data, [
            'recipient_id' => 'required|exists:users,id',
            'request_wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|max:3',
        ]);

        if ($validated->fails()) {
            return new ApiResponseDTO(false, 'Validation failed', $validated->errors(), 422);
        }

        // Ensure the user requesting has a wallet in the specified currency
        $wallet = Wallet::where('id', $data['request_wallet_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Create transaction request
        $transactionRequest = TransferRequest::create([
            'requester_id' => Auth::id(),
            'recipient_id' => $data['recipient_id'],
            'wallet_id' => $data['request_wallet_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => 'pending',
        ]);

        return new ApiResponseDTO(true, 'Request placed successfully', $transactionRequest);
    }

    /**
     * Accept a money request.
     */
    public function acceptRequest($id) : ApiResponseDTO
    {
        DB::beginTransaction();

        try 
        {
            $transactionRequest = TransferRequest::where('id', $id)
            ->where('recipient_id', Auth::id())
            ->where('status', 'pending')
            ->first();

            if(!$transactionRequest){
                return new ApiResponseDTO(false, 'Invalid request', null, 400);
            }

            // Perform money transfer (similar to normal send money)
            $senderWallet = Wallet::where('user_id', Auth::id())
                ->where('currency', $transactionRequest->currency)
                ->firstOrFail();


            $recipientWallet = Wallet::with('user')->where('id', $transactionRequest->wallet_id)->firstOrFail();

            if ($senderWallet->balance < $transactionRequest->amount) {
                return new ApiResponseDTO(false, 'Insufficient balance', null, 400);
            }

            // Fetch currency exchange rate
            $exchangeRate = CurrencyRate::where('from_currency', $transactionRequest->currency)
            ->where('to_currency', $transactionRequest->currency)
            ->value('rate');

            $amount = $exchangeRate * $transactionRequest->amount;

            // Deduct from sender
            $senderWallet->update(['balance' => $senderWallet->balance - $amount]);

             // Add to recipient
            $recipientWallet->update(['balance' => $recipientWallet->balance + $amount]);

            // Update request status
            $transactionRequest->update(['status' => 'completed']);

            // Create transaction record
            $transaction = Transaction::create([
                'transaction_reference' => Str::uuid(),
                'sender_wallet_id' => $senderWallet->id,
                'receiver_wallet_id' => $recipientWallet->id,
                'amount' => $amount,
                'converted_amount' => $amount,
                'currency' => $transactionRequest->currency,
                'to_currency' => $transactionRequest->currency,
                'exchange_rate' => $exchangeRate,
                'status' => 'completed',
                'description' => 'Accepted Transfer Request from '.$recipientWallet->user->email
            ]);

            DB::commit();
            return new ApiResponseDTO(true, null, 'Request accepted and money transferred successfully', 200);

        }catch(\Exception $e)
        {
            DB::rollBack();
            return new ApiResponseDTO(false, null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reject a money request.
     */
    public function rejectRequest($id): ApiResponseDTO
    {
        DB::beginTransaction();

        try {
            $transactionRequest = TransferRequest::where('id', $id)
                ->where('recipient_id', Auth::id())
                ->where('status', 'pending')
                ->first();
    
            if (!$transactionRequest) {
                return new ApiResponseDTO(false, 'Invalid request', null, 400);
            }
    
            $transactionRequest->update(['status' => 'rejected']);
    
            DB::commit();
            
            return new ApiResponseDTO(true, 'Request rejected successfully', null, 200);
        } catch (\Exception $e) {
            DB::rollBack();
    
            Log::error('Transaction request rejection failed', [
                'error' => $e->getMessage(),
                'request_id' => $id,
                'recipient_id' => Auth::id(),
            ]);
    
            return new ApiResponseDTO(false, 'Failed to reject request', null, 500);
        }
    }

    /**
     * Get all transactions
     */
    public function getAllTransactions(array $filters): ApiResponseDTO
    {
        try {
            $query = Transaction::query()->with(['senderWallet.user', 'receiverWallet.user']);

            if (!empty($filters['user_id'])) {
                $query->where(function ($q) use ($filters) {
                    $q->whereHas('senderWallet', function ($q) use ($filters) {
                        $q->where('user_id', $filters['user_id']);
                    })->orWhereHas('receiverWallet', function ($q) use ($filters) {
                        $q->where('user_id', $filters['user_id']);
                    });
                });
            }

            if (!empty($filters['currency'])) {
                $query->where('currency', $filters['currency']);
            }

            if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                $query->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
            }

            $transactions = $query->paginate(10);

            return new ApiResponseDTO(true, $transactions, 'Transactions retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error fetching transactions', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to retrieve transactions', null, 500);
        }
    }

    /**
     * Get single transaction
     */
    public function getTransactionById(int $transactionId): ApiResponseDTO
    {
        try {
            $transaction = Transaction::with(['senderWallet.user', 'receiverWallet.user'])
                ->find($transactionId);

            if (!$transaction) {
                return new ApiResponseDTO(false, 'Transaction not found', null, 404);
            }

            return new ApiResponseDTO(true, $transaction, 'Transaction retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error fetching transaction details', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to retrieve transaction', null, 500);
        }
    }

}
