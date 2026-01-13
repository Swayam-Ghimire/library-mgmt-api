<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveLoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_name' => $this->user->name,
            'book' => $this->book->title,
            'author' => $this->book->author,
            'borrowed_at' => $this->borrowed_at?->format('Y-m-d'),
            'borrower' => $this->user->name,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'days_remaining' => (int) max(0, now()->diffInDays($this->due_date, false)),
            'fine' => $this->fine ? $this->fine->amount : 0,
            'status' => $this->due_date < now() ? 'Overdue' : 'Due',
        ];
    }
}
