<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Positions extends Model
{
    protected $fillable = [
        'position_name',
        'position_description'
    ];

    public function uniformIssuanceRecipient() : HasMany {
        return $this->hasMany(UniformIssuanceRecipients::class, 'position_id', 'id');
    }

    public function uniformSet() : HasMany {
        return $this->hasMany(UniformSets::class);
    }
}
