<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = ['loan_id', 'amount', 'issued_at', 'paid_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'issued_at' => 'date',
        'paid_at' => 'date',
    ];

    public function loan(): BelongsTo {
        return $this->belongsTo(Loan::class);
    }
}
