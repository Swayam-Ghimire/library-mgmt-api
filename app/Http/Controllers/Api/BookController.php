<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookListRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        // Paginated list of books
        $books = Book::paginate(10);

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        // Details of a specific books
        return new BookResource($book);
    }

    public function search(BookListRequest $request)
    {
        // Search by title, author or isbn
        $searchTerm = $request->validated()['search'] ?? null;
        $books = Book::when($searchTerm, function ($query) use ($searchTerm) {
            return $query->where('title', 'LIKE', '%'.$searchTerm.'%')
                ->orWhere('isbn', $searchTerm)
                ->orWhere('author', 'LIKE', '%'.$searchTerm.'%');
        })->paginate(10);
        return BookResource::collection($books);
    }
}
