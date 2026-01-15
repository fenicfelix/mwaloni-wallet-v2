<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_token',
        'account_id',
        'requested_at',
        'expires_at'
    ];

    public $timestamps = false;
}
