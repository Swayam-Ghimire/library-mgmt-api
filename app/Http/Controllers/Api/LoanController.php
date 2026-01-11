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
        if (! $book || $book->quantity <= 0) {
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

    public function return()
    {
        // Return the borrowed book or delete loan and +1 quantity in the books table record

    }
}
