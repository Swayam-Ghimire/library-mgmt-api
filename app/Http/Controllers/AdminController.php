<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateBookRequest;
use App\Http\Requests\Admin\EditBookRequest;
use App\Http\Requests\Admin\IssueFineRequest;
use App\Http\Requests\Admin\PaidAmountRequest;
use App\Http\Resources\ActiveLoanResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserProfileResource;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Status;
use App\Models\User;

class AdminController extends Controller
{
    public function create(CreateBookRequest $request)
    {
        // Add new book to database
        $books = Book::create($request->validated());

        return new BookResource($books);
    }

    public function delete(Book $book)
    {
        // Delete a book record
        $deletedBook = $book;
        if ($book->delete()) {
            return (new BookResource($deletedBook))->additional(['meta' => ['message' => 'The book record is deleted',
                'status' => 'success'],
            ]);
        }
    }

    public function update(Book $book, EditBookRequest $request)
    {
        // Edit the book record by isbn
        $validate = $request->validated();
        $book->update($validate);

        return new BookResource($book);
    }

    public function issue(Loan $loan, IssueFineRequest $request)
    {
        // Issue a fine for a loan with passed due data and update status
        // includes two database operations.
        // Check if loan is already returned
        if ($loan->returned_date) {
            return response()->json([
                'message' => 'Book has been returned, cannot issue fine'
            ], 400);
        }

        if (! is_null($loan->paid_at)) {
            return response()->json([
                'message' => 'Fine has been paid, cannot issue another fine'
            ], 400);
        }

        // Check if fine already exists
        if ($loan->fine) {
            return response()->json([
                'message' => 'Fine already exists for this loan',
                'id' => $loan->fine->id,
                'fine' => $loan->fine->amount,
                'issued_at' => $loan->fine->issued_at->format('Y-m-d'),
            ], 400);
        }

        $fine = Fine::create([
            'loan_id' => $loan->id,
            'amount' => $request->amount,
            'issued_at' => now(),
        ]);

        // Update loan status to "fined" or keep as overdue
        $finedStatus = Status::where('name', 'overdue')->first();
        
        if ($finedStatus) {
            $loan->update(['status_id' => $finedStatus->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom fine issued successfully',
            'fine' => [
                'id' => $fine->id,
                'amount' => $fine->amount,
                'issued_at' => $fine->issued_at->format('Y-m-d'),
                'loan_details' => [
                    'id' => $loan->id,
                    'book' => $loan->book->title,
                    'borrower' => $loan->user->name,
                    'due_date' => $loan->due_date->format('Y-m-d'),
                ]
            ]
        ], 201);

    }

    public function members()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'member');
        })->with('roles')->paginate(10);

        return UserListResource::collection($users);
    }

    public function member_info(User $member)
    {
        // Get details of member including fine and loan history
        return new UserProfileResource($member->load(['loans.book', 'loans.fine', 'roles']));

    }

    public function active_loans()
    {
        // Get the list of active loans
        $loans = Loan::whereNull('returned_date')->with(['user:id,name', 'book:id,title', 'fine'])->paginate(10);
        
        return ActiveLoanResource::collection($loans);
    }

    public function paid(Fine $fine, PaidAmountRequest $request)
{
    // Check if already paid
    if ($fine->paid_at) {
        return response()->json([
            'message' => 'Fine is already paid',
            'paid_at' => $fine->paid_at->format('Y-m-d'),
        ], 400);
    }

    $validated = $request->validated();
    
    // Validate that paid amount is at least the fine amount
    if ($validated['amount'] < $fine->amount) {
        return response()->json([
            'message' => 'Paid amount must be at least the fine amount',
            'fine_amount' => $fine->amount,
            'paid_amount' => $validated['amount'],
        ], 422);
    }

    // Update the fine
    $fine->update([
        'paid_at' => now(),
        'amount' => $validated['amount'], // Update amount if paying more
    ]);

    if ($fine->loan) {
        $paidStatus = Status::where('name', 'returned')->first();
        if ($paidStatus) {
            $fine->loan->update(['status_id' => $paidStatus->id]);
        }
    }

    return response()->json([
        'message' => 'Fine marked as paid successfully',
        'fine' => [
            'id' => $fine->id,
            'original_amount' => $fine->getOriginal('amount'),
            'paid_amount' => $fine->amount,
            'paid_at' => $fine->paid_at?->format('Y-m-d'),
        ],
    ], 200);
}
}
