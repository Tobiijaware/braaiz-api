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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_reference')->unique(); 
            $table->foreignId('sender_wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->foreignId('receiver_wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('converted_amount', 15, 2)->nullable(); 
            $table->string('currency', 3);
            $table->string('to_currency', 3)->nullable(); 
            $table->decimal('exchange_rate', 10, 6)->nullable(); 
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
