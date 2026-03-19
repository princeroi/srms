<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'billing_id',
        'collect_by',
        'payment_date',
        'amount_paid',
        'payment_method',
        'reference_number',
    ];

    protected $casts = [
        'paymanet_date'         =>'date',
        'amount_paid'           =>'decimal:2',
    ];

    public function billing() : belongsTo {
        return $this->belongsTo(Billing::class, 'billing_id', 'id');
    }
}
