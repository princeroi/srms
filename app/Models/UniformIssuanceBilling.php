<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformIssuanceBilling extends Model
{
    protected $fillable =[
        'uniform_issuance_id', 
        'billed_to', 
        'billing_type', 
        'billing_items', 
        'total_price', 
        'status',
        'billed_at',
        'created_by'
    ];

    protected $casts = [
        'billing_items'         => 'array',
        'billing_at'            => 'date',
        'total_price'           => 'decimal:2'
    ];

    public function issuance() : BelongsTo
    {
        return $this->belongsTo(UniformIssuance::class, 'uniform_issuance_id');
    }

    public function creator() : BelongsTo 
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
