<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssuanceDr extends Model
{
    protected $table = 'issuance_dr';

    protected $fillable = [
        'for_delivery_receipt_id',
        'dr_number',
    ];

    public function forDeliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(ForDeliveryReceipt::class);
    }
}