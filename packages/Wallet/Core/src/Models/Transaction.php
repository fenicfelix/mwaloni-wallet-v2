<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Enums\TransactionType;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'account_number',
        'account_name',
        'account_reference',
        'reference',
        'description',
        'account_id',
        'service_id',
        'transaction_type',
        'transaction_date',
        'order_number',
        'message_id',
        'key_block',
        'status',
        'requested_amount',
        'disbursed_amount',
        'transaction_charges',
        'system_charges',
        'sms_charges',
        'revenue',
        'receipt_number',
        'result_description',
        'requested_on',
        'requested_by',
        'reversed_on',
        'reversed_by',
        'completed_at',
        'payment_channel_id',
    ];

    public $timestamps = false;

    // cast
    protected $casts = [
        'transaction_date' => 'datetime',
        'requested_on' => 'datetime',
        'reversed_on' => 'datetime',
        'completed_at' => 'datetime',
        'status' => TransactionStatus::class,
        'transaction_type' => TransactionType::class
    ];

    public function payload()
    {
        return $this->hasOne(TransactionPayload::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where("status", TransactionStatus::SUCCESS);
    }

    public function scopeUnsuccessful($query)
    {
        return $query->whereNotIn("status", [TransactionStatus::SUCCESS, TransactionStatus::CANCELLED]);
    }

    public function scopeFailed($query)
    {
        return $query->where("status", TransactionStatus::FAILED);
    }

    public function scopePending($query)
    {
        return $query->where("status", TransactionStatus::PENDING);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, "service_id");
    }

    public function account()
    {
        return $this->belongsTo(Account::class, "account_id");
    }

    public function paymentChannel()
    {
        return $this->belongsTo(PaymentChannel::class, "payment_channel_id");
    }
}
