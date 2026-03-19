<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformIssuanceRecipients extends Model
{
    protected $fillable = [
        'uniform_issuance_id',
        'transaction_id',
        'employee_name',
        'position_id',
        'employee_status',
        'uniform_set_id',
    ];

    public function uniformIssuance() : BelongsTo {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id', 'id');
    }

    public function uniformIssuanceItem() : HasMany {
        return $this->hasMany(UniformIssuanceItems::class, 'uniform_issuance_recipient_id', 'id');
    }

    public function position() : BelongsTo {
        return $this->belongsTo(Positions::class, 'position_id', 'id');
    }

    public function uniformSet() : BelongsTo {
        return $this->belongsTo(UniformSets::class, 'uniform_set_id', 'id');
    }

    protected static function booted(): void {
        static::creating(function (UniformIssuanceRecipients $recipient) {
            $date = now()->format('Ymd');

            $latest = static::orderByDesc('id')->first();

            if ($latest && $latest->transaction_id) {
                $lastSequence = (int) substr($latest->transaction_id, -4);
                $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $sequence = '0001';
            }

            $recipient->transaction_id = "TXN-{$date}-{$sequence}";
        });
    }
}
