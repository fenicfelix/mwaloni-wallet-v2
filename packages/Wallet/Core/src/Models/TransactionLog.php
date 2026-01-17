<?php

namespace Wallet\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'type_id',
        'account_id',
        'client_id',
        'service_id',
        'amount',
        'reference',
        'status',
        'status_description',
        'initiated_by',
        'initiated_on'
    ];

    public $timestamps = false;

    public function service()
    {
        return $this->belongsTo(Service::class, "service_id");
    }

    public function account()
    {
        return $this->belongsTo(Account::class, "account_id");
    }

    public function client()
    {
        return $this->belongsTo(Client::class, "client_id");
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, "initiated_by");
    }
}
