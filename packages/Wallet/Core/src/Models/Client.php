<?php

namespace Wallet\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'name',
        'client_id',
        'account_manager',
        'balance',
        'active',
        'added_by',
        'updated_by'
    ];

    // cast
    protected $casts = [
        'active' => 'boolean',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, "account_manager");
    }

    public function registrar()
    {
        return $this->belongsTo(User::class, "added_by");
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', "1");
    }
}
