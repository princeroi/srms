<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformRestockLogs extends Model
{
    protected $fillable = [
        'uniform_restock_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];
 
    public function uniformRestock()
    {
        return $this->belongsTo(UniformRestocks::class);
    }
 
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
