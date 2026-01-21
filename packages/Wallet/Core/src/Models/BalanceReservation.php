<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceReservation extends Model
{
    protected $fillable = [
        'account_id',
        'transaction_id',
        'amount'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
