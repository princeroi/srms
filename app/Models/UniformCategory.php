<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformCategory extends Model
{
    protected $fillable = [
        'uniform_category_name'
    ];

    public function uniformItem() : HasMany {
        return $this->hasMany(uniformItem::class, 'uniform_category_id', 'id');
    }
}
