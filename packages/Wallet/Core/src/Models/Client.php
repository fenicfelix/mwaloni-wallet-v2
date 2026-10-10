<?php

namespace Wallet\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'account_manager',
        'balance',
        'active',
    ];

    // cast
    protected $casts = [
        'active' => 'boolean',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, "account_manager");
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('active', "1");
    }
}
