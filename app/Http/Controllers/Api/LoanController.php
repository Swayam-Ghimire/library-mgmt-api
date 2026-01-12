<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function loans(Request $request)
    {
        // View user Borrowing history
        $user = $request->user();
        $loan = $user->loans()->with(['book', 'status'])->paginate(10);

        return LoanResource::collection($loan);
    }

    public function borrow(Book $book, Request $request)
    {
        // Borrow a book or Create loan
        if ($book->quantity <= 0) {
            return response()->json([
                'message' => 'Book is currently unavailable',
            ], 404);
        }
        $user = $request->user();
        if (Loan::alreadyHas($user, $book)) {
            return response()->json(['message' => 'You already have an active loan for this book'], 422);
        }

        $status = Status::where('name', 'borrowed')->first();

        return DB::transaction(function () use ($book, $user, $status) {
            $book->decrement('quantity');
            $loan = $user->loans()->create([
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_date' => now()->addMonth(),
                'status_id' => $status->id,
            ]);

            return new LoanResource($loan->load(['book', 'status']));
        });
    }

    public function return(Loan $loan, Request $request)
    {
        $user = $request->user();

        // 1. Check if loan belongs to user
        if ($loan->user_id !== $user->id) {
            return response()->json([
                'message' => 'You have not borrowed this book',
            ], 403);
        }

        // 2. Check if already returned 
        if (! is_null($loan->returned_date)) {
            return response()->json([
                'message' => 'You have already returned this book',
                'returned_at' => $loan->returned_date->format('Y-m-d'),
            ], 400);
        }

        // 3. Get returned status
        $status = Status::firstWhere('name', 'returned');

        return DB::transaction(function () use ($loan, $status) {
            // 4. Increment book quantity
            $loan->book()->increment('quantity');

            // 5. Update loan
            $loan->update([
                'returned_date' => now(), 
                'status_id' => $status->id,
            ]);

            // 6. Refresh with relationships
            $loan->load(['book', 'user', 'status']);

            return response()->json([
                'message' => 'Book returned successfully',
                'loan' => new LoanResource($loan),
            ]);
        });
    }
}
