<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'profile' => [
                'name' => $this->name,
                'email' => $this->email,
                'membership_since' => $this->created_at?->format('F Y'),

            ],
            
            'current_loans' => $this->whenLoaded('loans', function () {
                return $this->loans
                    ->whereNull('returned_date')
                    ->map(function ($loan) {
                        return [
                            'book' => $loan->book->title ?? 'Unknown Book',
                            'due_date' => $loan->due_date?->format('M d'),
                            'status' => $loan->due_date < now() ? 'Overdue' : 'Due',
                            'fine' => $loan->fine ? $loan->fine->amount : 0,
                        ];
                    })->values();
            }, []),
        ];
    }
}