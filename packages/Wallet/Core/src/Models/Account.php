<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = [];

    // include operational_balance in the model's array and JSON form
    protected $appends = ['operational_balance'];

    // disable timestamps
    public $timestamps = false;

    protected $casts = [
        'active' => 'boolean',
        'auto_fetch_balance' => 'boolean',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, "account_type_id");
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, "currency_id");
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', "1");
    }

    public function getOperationalBalanceAttribute()
    {
        return ($this->utility_balance + $this->working_balance) - $this->withheld_amount;
    }
}
