<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'name',
        'service_id',
        'description',
        'active',
        'client_id',
        'system_charges',
        'sms_charges',
        'max_trx_amount',
        'username',
        'password',
        'added_by',
        'updated_by',
        'callback_url',
        'account_id'
    ];

    // cast
    protected $casts = [
        'active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, "client_id");
    }

    public function account()
    {
        return $this->belongsTo(Account::class, "account_id");
    }
}
