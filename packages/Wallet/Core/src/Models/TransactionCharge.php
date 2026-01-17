<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        "minimum",
        "maximum",
        "charge",
        "payment_channel_id"
    ];

    public function channel()
    {
        return $this->belongsTo(PaymentChannel::class, "payment_channel_id");
    }
}
