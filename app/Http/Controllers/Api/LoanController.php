<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function loans()
    {
        // View user Borrowing history
    }

    public function borrow(Book $book, Request $request)
    {
        // Borrow a book or Create loan
        if (! $book || $book->quantity = 0) {
            return response()->json([
                'message' => 'Book is currently unavailable',
            ], 404);
        }
        $alreadyHas = $request->user()->loans()->where('book_id', $book->id)->whereNull('returned_at')->exists();

        if ($alreadyHas) {
            return response()->json(['message' => 'You already have an active loan for this book'], 422);
        }

        $status = Status::where('name', 'borrowed')->first();

        $results = DB::transaction(function () use ($book, $request, $status) {
            $book->decrement('quantity');
            $loan = Loan::create([
                'user_id' => $request->user()->id,
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_date' => now()->addMonth(),
            ]);
            $loan->status()->attach($status->id);
            return response()->json([
                'message' => 'Book borrowed',
                'borrowed_book' => $loan->load($book),
            201]);
        });
        
        return $results;
    }

    public function return()
    {
        // Return the borrowed book or delete loan and +1 quantity in the books table record
    }
}
