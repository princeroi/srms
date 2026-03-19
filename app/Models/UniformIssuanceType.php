<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformIssuanceType extends Model
{
    protected $fillable = [
        'uniform_issuance_type_name',
    ];

    public function uniformIssuance() : HasMany {
        return $this->hasMany(UniformIssuances::class, 'uniform_issuance_type_id', 'id');
    }
}
