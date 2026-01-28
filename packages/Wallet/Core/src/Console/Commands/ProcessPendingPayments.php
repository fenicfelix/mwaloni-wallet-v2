<?php

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Transaction;

class ProcessPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:process-pending-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to process all pending payments in the system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Processing pending payments...');
        // Here you would add the logic to process pending payments
        $chunk = 1;
        Transaction::where('status', TransactionStatus::PENDING)
            ->chunk($chunk, function ($transactions) {
                foreach ($transactions as $transaction) {
                    $this->info("Processing transaction Order Number: {$transaction->order_number}");
                    // Dispatch the job to process the payment
                    ProcessPayment::dispatch($transaction->id, $transaction->paymentChannel->slug)->onQueue('process-payments');
                }
            });
        $this->info('SUCCESS! The pending payments have been processed successfully');
        return Command::SUCCESS;
    }
}
