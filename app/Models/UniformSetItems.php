<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformSetItems extends Model
{
    protected $fillable = [
        'uniform_set_id',
        'uniform_item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity'          => 'integer',
    ];

    public function uniformSet() : BelongsTo {
        return $this->belongsTo(UniformSets::class, 'uniform_set_id', 'id');
    }

    public function uniformItem() : BelongsTo {
        return $this->belongsTo(UniformItems::class, 'uniform_item_id', 'id');
    }
}
