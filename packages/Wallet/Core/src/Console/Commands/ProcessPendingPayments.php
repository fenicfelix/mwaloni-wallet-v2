<?php

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:process-pending-payments {id?}';

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
        $id = $this->argument('id');

        if ($id) {
            return $this->processSpecificTransaction($id);
        }

        return $this->processAllPendingPayments();
    }

    private function processSpecificTransaction($id): int
    {
        $transaction = Transaction::where('order_number', $id)->first();
        if ($transaction) {
            $this->info("Processing transaction Order Number: {$transaction->order_number}");
            // Dispatch the job to process the payment
            app(TransactionRepository::class)->update($transaction->id, [
                'status' => TransactionStatus::PROCESSING,
            ]);
            ProcessPayment::dispatch($transaction->id, $transaction->paymentChannel->slug)->onQueue('process-payments');
            $this->info('SUCCESS! The pending payment has been processed successfully');
            return Command::SUCCESS;
        }

        $this->error("Transaction with Order Number: {$id} not found.");
        return Command::FAILURE;
    }

    private function processAllPendingPayments(): int
    {
        $chunk = 1;

        Transaction::where('status', TransactionStatus::PENDING)
            ->select(['id', 'order_number', 'payment_channel_id'])
            ->chunkById($chunk, function ($transactions) {

                $ids = $transactions->pluck('id');

                // ✅ ONE bulk update per chunk
                Transaction::whereIn('id', $ids)
                    ->update(['status' => TransactionStatus::PROCESSING]);

                foreach ($transactions as $transaction) {
                    $this->info("Processing transaction Order Number: {$transaction->order_number}");

                    ProcessPayment::dispatch(
                        $transaction->id,
                        $transaction->paymentChannel->slug
                    )->onQueue('process-payments');
                }
            });

        $this->info('SUCCESS! Pending payments processed');
        return Command::SUCCESS;
    }
}
