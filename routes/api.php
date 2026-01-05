<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\FineController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Routes for admin
Route::prefix('admin')->controller(AdminController::class)->group(function () {
    Route::post('/books/add', 'create');
    Route::delete('/books/{book}', 'delete');
    Route::get('/members', 'members');
    Route::get('/members/{member}', 'member_info');
    Route::get('/loans', 'active_loans');
    Route::post('/loans/{loan}/fine', 'issue');
    Route::put('/fines/{fine}/paid', 'paid');
});

// Routes for member
Route::get('/user/profile', [UserController::class, 'profile']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/loans/{book}/borrow', [LoanController::class, 'borrow']);
Route::post('/loans/{loan}/return', [LoanController::class, 'return']);
Route::get('/user/loans', [LoanController::class, 'loans']);
Route::get('/user/fines', [FineController::class, 'fines']);


// Common routes
Route::get('/books', [BookController::class, 'index']);
Route::get('/book/{book}', [BookController::class, 'show']);
Route::get('/books/search', [BookController::class, 'search']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
