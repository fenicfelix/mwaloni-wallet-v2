<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComposedMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        "identifier",
        "contacts",
        "body",
        "has_placeholders",
        "is_processed"
    ];

    // casts
    protected $casts = [
        'has_placeholders' => 'boolean',
        'is_processed' => 'boolean',
    ];
}
