<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookController extends Controller
{
    public function index() {
        // Paginated list of books
        $books = Book::paginate(10);
        return BookResource::collection($books);
    }

    public function show(Book $book) {
        // Details of a specific books
        return new BookResource($book);
    }

    public function search() {
        // Search by title, author or isbn
    }
}
