<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferRequest extends Model
{
    use HasFactory;

    protected $fillable = ['requester_id', 'recipient_id', 'wallet_id', 'amount', 'currency', 'current_rate', 'status'];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
