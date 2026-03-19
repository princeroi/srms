<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformItems extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uniform_category_id',
        'uniform_item_name',
        'uniform_item_description',
        'uniform_item_price',
        'uniform_item_image',
    ];

    protected $casts = [
        'uniform_item_price'        => 'decimal:2',
    ];

    public function category() : BelongsTo {
        return $this->belongsTo(UniformCategory::class, 'uniform_category_id', 'id');
    }

    public function itemVariant() : HasMany {
        return $this->hasMany(UniformItemVariants::class, 'uniform_item_id', 'id');
    }

    protected static function booted() : void {
        static::deleting(function (UniformItems $item) {
            $item->itemVariant()->each(fn ($variant) => $variant->delete()); // ✅ correct relation name
        });
        static::restoring(function (UniformItems $item) {
            $item->itemVariant()->withTrashed()->each(fn ($variant) => $variant->restore()); // ✅
        });
    }

    public function uniformSetItem() : HasMany {
        return $this->hasMany(UniformSetItems::class, 'uniform_item_id', 'id');
    }

    public function uniformIssuanceRecipient() : belongsTo {
        return $this->belongsTo(UniformIssuanceRecipients::class, 'uniform_items_id', 'id');
    }
}
