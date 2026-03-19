<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformSets extends Model
{
    protected $fillable = [
        'uniform_set_name',
        'uniform_set_description',
        'position_id',
        'site_id',
        'employee_status',
    ];

    public function uniformSetItem() : HasMany {
        return $this->hasMany(UniformSetItems::class, 'uniform_set_id', 'id');
    }

    public function position() : BelongsTo {
        return $this->belongsTo(Positions::class, 'position_id', 'id');
    }

    public function site() : BelongsTo {
        return $this->belongsTo(Sites::class, 'site_id', 'id');
    }

    public function uniformIssuanceRecipient() : HasMany {
        return $this->hasMany(UniformIssuanceRecipients::class, 'uniform_set_id', 'id');
    }
}
