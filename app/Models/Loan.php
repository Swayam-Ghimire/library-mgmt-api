<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $fillable = ['user_id', 'book_id', 'borrowed_at', 'due_date', 'returned_at', 'status_id'];

    public function status(): BelongsTo {
        return $this->belongsTo(Status::class);
    }

    public function fine(): HasOne {
        return $this->hasOne(Fine::class);
    }

    public function book(): BelongsTo {
        return $this->belongsTo(Book::class);
    }
    
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
