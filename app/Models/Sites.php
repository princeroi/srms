<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sites extends Model
{
    protected $fillable = [
        'client_id',
        'site_name',
        'site_location'
    ];

    public function client() : BelongsTo {
        return $this->belongsTo(Clients::class, 'client_id', 'id');
    }

    public function uniformIssuance() : HasMany {
        return $this->hasMany(UniformIssuances::class, 'site_id', 'id');
    }

    public function uniformSet() : HasMany {
        return $this->hasMany(UniformSets::class, 'site_id', 'id');
    }
}
