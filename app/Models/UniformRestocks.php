<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformRestocks extends Model
{
    protected $fillable = [
        'supplier_name',
        'ordered_by',
        'ordered_at',
        'status',
        'pending_at',
        'delivered_at',
        'partial_at',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'ordered_at'   => 'date',
        'pending_at'   => 'date',
        'delivered_at' => 'date',
        'partial_at'   => 'date',
        'cancelled_at' => 'date',
    ];

    public function uniformRestockItem(): HasMany
    {
        return $this->hasMany(UniformRestockItems::class, 'uniform_restock_id', 'id');
    }

    public function uniformRestockLog(): HasMany
    {
        return $this->hasMany(UniformRestockLogs::class, 'uniform_restock_id', 'id');
    }
}