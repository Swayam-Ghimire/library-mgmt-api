<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $fillable = ['user_id', 'book_id', 'borrowed_at', 'due_date', 'returned_date', 'status_id'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_date' => 'datetime',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function alreadyHas(User $user, Book $book): bool
    {
        return $user->loans()
            ->where('book_id', $book->id)
            ->where(function ($q) {
                $q->whereNull('returned_date')
                    ->orWhere('returned_date', '');
            })
            ->exists();
    }
}
