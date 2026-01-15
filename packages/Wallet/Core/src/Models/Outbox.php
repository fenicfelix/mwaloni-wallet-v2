<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outbox extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'message',
        'to',
        'sent',
        'created_at',
        'sent_at',
        'cost'
    ];

    public $timestamps = false;

    // casts
    protected $casts = [
        'sent' => 'boolean',
    ];
}
