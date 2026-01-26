<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = [];

    // include float in the model's array and JSON form
    protected $appends = ['float'];

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

    // balance reservation
    public function balanceReservation()
    {
        return $this->hasMany(BalanceReservation::class);
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', "1");
    }

    public function scopeIsAutoFetch($query)
    {
        return $query->where('auto_fetch_balance', "1");
    }

    public function getFloatAttribute()
    {
        $balanceReservations = 0;
        try {
            $balanceReservations = $this->balanceReservation()->sum('amount');
        } catch (\Throwable $th) {
            //throw $th;
        }
        return ($this->utility_balance + $this->working_balance) - ($this->revenue + $this->withheld_amount + $balanceReservations);
    }
}
