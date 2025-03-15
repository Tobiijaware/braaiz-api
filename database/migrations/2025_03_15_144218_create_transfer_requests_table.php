<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade'); // User requesting money
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade'); // User receiving request
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade'); // Target wallet
            $table->decimal('amount', 15, 2);
            $table->string('currency');
            $table->decimal('current_rate', 15, 6)->nullable();
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
