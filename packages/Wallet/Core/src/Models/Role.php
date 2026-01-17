<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Wallet\Core\Http\Enums\PermissionType;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'permission_type',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
        'permission_type' => PermissionType::class,
    ];
}
