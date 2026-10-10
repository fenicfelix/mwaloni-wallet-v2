<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'callback_url',
        'account_id'
    ];

    // cast
    protected $casts = [
        'active' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, "client_id");
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, "account_id");
    }
}
