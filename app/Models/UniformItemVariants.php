<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UniformItems;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformItemVariants extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'uniform_item_id',
        'uniform_item_size',
        'uniform_item_quantity'
    ];

    protected $casts = [
        'uniform_item_quantity'         => 'integer'
    ];

    public function uniformItem() : BelongsTo {
        return $this->belongsTo(UniformItems::class, 'uniform_item_id', 'id')
                    ->withTrashed();
    }
    
}


