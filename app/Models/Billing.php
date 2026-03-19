<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Billing extends Model
{
    protected $fillable = [
        'invoice_number',
        'client_id',
        'notes',
        'billing_start_period',
        'billing_end_period',
        'billing_date',
        'due_date',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'billing_start_period'      =>'date',
        'billing_end_period'        =>'date',
        'billing_date'              =>'date',
        'due_date'                  =>'date',
        'total_amount'              =>'decimal:2',
    ];

    public function client() : BelongsTo {
        return $this->belongsTo(Clients::class, 'client_id', 'id');
    }

    public function collection() : HasMany {
        return $this->hasMany(Collection::class, 'billing_id', 'id');
    }

    public function getTotalPaidAttribute() {
        return $this->collection()->sum('amount_paid');
    }

    public function getRemainingBalanceAttribute() {
        return $this->total_amount - $this->total_paid;
    }
}
