<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayload extends Model
{
    use HasFactory;

    // fillable fields
    protected $fillable = [
        'raw_request',
        'trx_payload',
        'raw_callback',
        'transaction_id'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
