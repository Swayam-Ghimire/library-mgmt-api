<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasUuids;
    protected $fillable = ['title', 'author', 'isbn', 'genre', 'description', 'quantity'];

    protected $hidden = ['created_at', 'updated_at'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
