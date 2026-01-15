<?php

namespace Wallet\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemPreference extends Model
{
    use HasFactory;

    public $fillable = ["identifier", "title", "slug", "value", "updated_at", "updated_by"];
}
