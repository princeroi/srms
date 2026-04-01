<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformIssuanceBillingAtd extends Model
{
    
    protected $fillable = [
        'uniform_issuance_id',
        'uniform_issuance_billing_id',
        'employee_name',
        'date_signed',
        'atd_image',
        'remarks',
        'uploaded_by',
    ];

    protected $casts = [
        'date_signed' => 'date',
    ];

    public function issuance(): BelongsTo
    {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id');
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(UniformIssuanceBilling::class, 'uniform_issuance_billing_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}