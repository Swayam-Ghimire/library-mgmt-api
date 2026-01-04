<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasUuids;
    protected $fillable = ['title', 'author', 'isbn', 'genre', 'description', 'quantity'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
