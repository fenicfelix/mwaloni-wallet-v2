<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_transactions',
        'successful_transactions',
        'pending_transactions',
        'failed_transactions',
        'total_spent',
        'total_revenue',
        'available_revenue',
        'total_sms_cost',
    ];

    // cast
    protected $casts = [
        'total_transactions' => 'integer',
        'pending_transactions' => 'integer',
        'failed_transactions' => 'integer',
        'total_spent' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'available_revenue' => 'decimal:2',
        'total_sms_cost' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
