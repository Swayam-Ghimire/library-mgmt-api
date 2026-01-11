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
            'returned_on' => $this->returned_at?->format('Y-m-d') ?? 'Not returned yet',
            // Show the status name instead of the status_id
            'status' => $this->status->name,
            'is_overdue' => $this->due_date < now() && !$this->returned_at,
        ];
    }
}
