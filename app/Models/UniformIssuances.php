<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformIssuances extends Model
{
    protected $fillable = [
        'site_id',
        'uniform_issuance_type_id',
        'uniform_issuance_status',
        'pending_at',
        'partial_at',
        'issued_at',
        'cancelled_at',
        'signed_receiving_copy',
        'notes',
        'is_for_transmit'
    ];

    protected $casts = [
        'pending_at'            => 'date',
        'partial_at'            => 'date',
        'issued_at'             => 'date',
        'cancelled_at'          => 'date',
    ];

    public function site() : BelongsTo {
        return $this->belongsTo(Sites::class, 'site_id', 'id');
    }

    public function uniformIssuanceType() : BelongsTo {
        return $this->belongsTo(UniformIssuanceType::class, 'uniform_issuance_type_id', 'id');
    }

    public function uniformIssuanceRecipient() : HasMany {
        return $this->hasMany(UniformIssuanceRecipients::class, 'uniform_issuance_id', 'id');
    }

    public function uniformIssuanceLogs() : HasMany
    {
        return $this->hasMany(UniformIssuanceLog::class, 'uniform_issuance_id');
    }

    
}
