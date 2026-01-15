<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        "identifier",
        "name",
        "code",
        "active"
    ];

    public $timestamps = false;

    protected $casts = [
        'active' => 'boolean',
    ];
}
