<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionPayload extends Model
{
    use HasFactory;

    // fillable fields
    protected $fillable = [
        'raw_request',
        'trx_payload',
        'raw_callback',
        'file_path',
        'transaction_id'
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
