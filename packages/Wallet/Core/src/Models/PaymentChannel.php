<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        "identifier",
        "name",
        "slug",
        "description",
        "active"
    ];

    public $timestamps = false;

    // cast
    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
