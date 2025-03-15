<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Classes\TransactionService;
use App\Models\Wallet;
use App\Models\User;
use App\Models\CurrencyRate;
use App\Models\Transaction;
use App\DTOs\ApiResponseDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransferTest extends TestCase
{
    use RefreshDatabase; // Clears DB before each test

    public function test_transfer_success()
    {
        // Create a user to associate with the wallet
        $senderUser = User::factory()->create();
        $receiverUser = User::factory()->create();

        // Create test wallets with user_id
        $senderWallet = Wallet::factory()->create([
            'balance' => 500,
            'user_id' => $senderUser->id,
        ]);
        $receiverWallet = Wallet::factory()->create([
            'balance' => 100,
            'user_id' => $receiverUser->id,
        ]);

        // Create exchange rate
        CurrencyRate::create([
            'from_currency' => 'USD',
            'to_currency' => 'EUR',
            'rate' => 0.85
        ]);

        // Transaction data
        $data = [
            'sender_wallet_id' => $senderWallet->id,
            'receiver_wallet_id' => $receiverWallet->id,
            'amount' => 100,
            'from_currency' => 'USD',
            'to_currency' => 'EUR',
            'description' => 'Test transfer'
        ];

        // Instantiate service
        $service = new TransactionService();

        // Call transfer method
        $response = $service->transfer($data);

        // Assert transfer was successful
        $this->assertTrue($response->success);
        // $this->assertEquals('Transfer successful', $response->message);

        // Reload wallets from the database
        $senderWallet->refresh();
        $receiverWallet->refresh();

        // Assert sender balance reduced
        $this->assertEquals(400, $senderWallet->balance); // 500 - 100

        // Assert receiver balance increased
        $this->assertEquals(185, $receiverWallet->balance); // 100 + (100 * 0.85)
    }

}
