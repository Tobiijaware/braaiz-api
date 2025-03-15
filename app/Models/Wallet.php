<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\CurrencyEnum;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'currency', 'balance'];

    protected $casts = [
        'currency' => CurrencyEnum::class, // Cast currency to Enum
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sentTransactions()
    {
        return $this->hasMany(Transaction::class, 'sender_wallet_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'receiver_wallet_id');
    }

}
