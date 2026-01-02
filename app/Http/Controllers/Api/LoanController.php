<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function loans() {
        // View user Borrowing history
    }

    public function borrow() {
        // Borrow a book or Create loan
    }

    public function return() {
        // Return the borrowed book or delete loan and +1 quantity in the books table record
    }
}
