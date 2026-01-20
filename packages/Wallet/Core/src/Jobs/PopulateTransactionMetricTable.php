<?php

namespace Wallet\Core\Jobs;

use Wallet\Core\Models\Account;
use Wallet\Core\Models\Outbox;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Models\TransactionMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateTransactionMetricTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $total_transactions = Transaction::count();
            $successful_transactions = Transaction::successful()->count('id');
            $pending_transactions = Transaction::unsuccessful()->count('id');
            $failed_transactions = Transaction::failed()->count('id');
            $total_spent = Transaction::select(DB::raw('sum(disbursed_amount+system_charges+sms_charges) as total'))->get()[0];
            $total_revenue = Transaction::sum('revenue');
            $available_revenue = Account::sum('revenue');
            $total_sms_cost = Outbox::sum('cost');

            // create or update
            $update = TransactionMetric::updateOrCreate(
                ['id' => 1],
                [
                    'total_transactions' => $total_transactions,
                    'successful_transactions' => $successful_transactions,
                    'pending_transactions' => $pending_transactions,
                    'failed_transactions' => $failed_transactions,
                    'total_spent' => round($total_spent->total ?? 0, 2),
                    'total_revenue' => round($total_revenue ?? 0, 2),
                    'available_revenue' => round($available_revenue ?? 0, 2),
                    'total_sms_cost' => round($total_sms_cost ?? 0, 2),
                ]
            );

            if (! $update) {
                throw new \Exception('Failed to update transaction metrics.');
            }

            Log::info('Transaction Metrics populated successfully.');
        } catch (\Throwable $th) {
            Log::error('Transaction Metrics Error: ' . $th->getMessage());
            throw $th;
        }
    }
}
