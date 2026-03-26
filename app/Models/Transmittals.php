<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transmittals extends Model
{
    protected $fillable = [
        'uniform_issuance_id',
        'transmittal_number',
        'transmitted_by',
        'transmitted_to',
        'items_summary',
        'purpose',
        'instructions',
        'transmitted_at',
        'status',
    ];
 
    protected $casts = [
        'items_summary'  => 'array',
        'transmitted_at' => 'date',
    ];
 
    public function uniformIssuance(): BelongsTo
    {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id');
    }

    public function issuances()
    {
        return $this->belongsToMany(
            \App\Models\UniformIssuances::class,
            'transmittal_issuances',
            'transmittal_id',
            'uniform_issuance_id'
        );
    }
    
 
    /**
     * Auto-generate transmittal number: TXN-YYYYMMDD-XXXX
     */
    public static function generateNumber(): string
    {
        $prefix = 'TXN-' . now()->format('Ymd') . '-';
        $last   = static::where('transmittal_number', 'like', $prefix . '%')
            ->orderByDesc('transmittal_number')
            ->value('transmittal_number');
 
        $next = $last
            ? (int) substr($last, -4) + 1
            : 1;
 
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
