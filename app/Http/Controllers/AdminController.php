<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;

class AdminController extends Controller
{
    public function create(CreateBookRequest $request) {
       // Add new book to database
        $books = Book::create($request->validated());
        return new BookResource($books);
    }

    public function delete() {
        // Delete a book record
    }

    public function issue() {
        // Issue a fine for a loan with passed due data and update status
        // includes two database operations.
    }

    public function members() {
        // Get list of members
    }

    public function member_info() {
        // Get details of member including fine and loan history
    }    

    public function active_loans() {
        // Get the list of active loans
    } 

    public function paid() {
        // Mark fine as paid and update paid at
    }
}
