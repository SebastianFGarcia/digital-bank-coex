<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'wallet_id',
        'type_transaction_id',
        'user_id',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function typeTransaction()
    {
        return $this->belongsTo(TypeTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
