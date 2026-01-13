<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'book' => [
                'title' => $this->book->title,
                'author' => $this->book->author,
                'genre' => $this->book->genre,
            ],
            // Format dates into a readable string
            'borrowed_on' => $this->borrowed_at?->format('Y-m-d'),
            'due_on' => $this->due_date?->format('Y-m-d'),
            'returned_on' => $this->returned_date?->format('Y-m-d') ?? 'Not returned yet',
            'status' => $this->status->name,
            'fine' => $this->fine ? $this->fine->amount : 0,
        ];
    }
}
