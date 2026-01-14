<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function fines(Request $request) {
        // View all fines issued to member
        $user = $request->user();

        // Get fines with book and user details
        $fines = $user->loans()
            ->whereHas('fine')
            ->with(['fine', 'book:id,title', 'user:id,name'])
            ->get()
            ->map(function ($loan) {
                return [
                    'fine_id' => $loan->fine->id,
                    'amount' => $loan->fine->amount,
                    'issued_at' => $loan->fine->issued_at?->format('Y-m-d'),
                    'paid_at' => $loan->fine->paid_at?->format('Y-m-d'),
                    'status' => $loan->fine->paid_at ? 'Paid' : 'Pending',
                    'book_name' => $loan->book->title,
                    'username' => $loan->user->name,
                ];
            });

        return response()->json([
            'fines' => $fines
        ]);
    }
}
