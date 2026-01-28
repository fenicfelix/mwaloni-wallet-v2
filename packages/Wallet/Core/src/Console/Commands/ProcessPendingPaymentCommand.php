<?php

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Transaction;

class ProcessPendingPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:process-pending-payment {id}';

    protected $description = 'Process pending payments in the wallet';

    public function handle()
    {
        // Logic to process pending payments
        $this->info('Processing pending payment...');
        $orderNumber = $this->argument('id');
        $this->info("Processing transaction ID: {$orderNumber}");
        $transaction = Transaction::with('paymentChannel')->where('order_number', $orderNumber)->first();
        if (! $transaction) {
            $this->error("Transaction with ID {$orderNumber} not found.");
            return;
        }

        // ProcessPayment::dispatch($transaction->id, $transaction->paymentChannel->slug)->onQueue('process-payments');
        $this->info('Payment processing job dispatched successfully.');
    }
}
