<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;

    public const TYPE_PAYMENTS = 1;
    public const TYPE_ACCOUNT_BALANCE = 2;
    public const TYPE_REVERSAL = 3;
    public const TYPE_CASHOUT = 4;
    public const TYPE_SEARVICE_CHARGE = 5;
    public const TYPE_DISTRIBUTE = 6;
    public const TYPE_WITHDRAW = 7;
    public const TYPE_REVENUE_TRANSFER = 8;

    protected $fillable = [
        "identifier",
        "name",
        "description"
    ];

    public $timestamps = false;
}
