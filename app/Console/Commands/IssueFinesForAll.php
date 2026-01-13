<?php

namespace App\Console\Commands;

use App\Models\Fine;
use App\Models\Loan;
use Illuminate\Console\Command;

class IssueFinesForAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issue:fines-for-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Issue fines for all overdue loans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // check if book is returned (status)
        // check books returned date
        // if overdue add fine to the existing fine or create new fine of 10

        $overdueLoans = Loan::with(['fine'])
            ->whereNull('returned_date')
            // ->where('due_date', '<', now())
            ->get();

        foreach ($overdueLoans as $loan) {
            // Check if fine already issued today
            if ($loan->fine && $loan->fine->issued_at->isSameDay(now())) {
                $this->info("Skipped Loan #{$loan->id}: Fine already issued today");
                continue; // Skip, already fined today
            }

            if ($loan->fine) {
                // Add 10 to existing fine
                $loan->fine->increment('amount', 10);
                $loan->fine->update(['issued_at' => now()]);
                $this->info("Updated fine for Loan #{$loan->id}: +10");
            } else {
                // Create new 10 fine
                Fine::create([
                    'loan_id' => $loan->id,
                    'amount' => 10,
                    'issued_at' => now(),
                ]);
                $this->info("Created fine for Loan #{$loan->id}: $10");
            }
        }

        $this->info('✅ Daily fine check completed');

    }
}
