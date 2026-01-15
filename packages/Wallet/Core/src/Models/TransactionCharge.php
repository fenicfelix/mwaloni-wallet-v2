<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        "identifier",
        "minimum",
        "maximum",
        "charge",
        "added_on",
        "added_by",
        "updated_on",
        "updated_by",
        "payment_channel_id"
    ];

    public $timestamps = false;

    public function channel()
    {
        return $this->belongsTo(PaymentChannel::class, "payment_channel_id");
    }
}
