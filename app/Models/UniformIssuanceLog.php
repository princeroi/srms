<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformIssuanceLog extends Model
{
    protected $fillable = [
        'uniform_issuance_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    public function uniformIssuance()
    {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}