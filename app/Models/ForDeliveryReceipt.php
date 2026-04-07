<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForDeliveryReceipt extends Model
{
    protected $table = 'for_delivery_receipts';

    protected $fillable = [
        'uniform_issuance_id',
        'endorse_by',
        'endorse_date',
        'item_summary',
        'status',
        'done_date',
        'cancel_date',
        'remarks',
    ];

    protected $casts = [
        'item_summary'  => 'array',
        'endorse_date'  => 'date',
        'done_date'     => 'date',
        'cancel_date'   => 'date',
    ];

    public function uniformIssuance(): BelongsTo
    {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id');
    }

    public function issuanceDrs(): HasMany
    {
        return $this->hasMany(IssuanceDr::class, 'for_delivery_receipt_id');
    }
}