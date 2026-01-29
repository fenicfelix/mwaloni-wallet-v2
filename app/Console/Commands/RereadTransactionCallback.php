<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Jobs\Daraja\ProcessDarajaPaymentStatusCallback;
use Wallet\Core\Repositories\TransactionRepository;

class RereadTransactionCallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:reread-transaction-callback {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reread the transaction callback';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        // Logic to reread the transaction callback
        $this->info("Rereading transaction callback for ID: $id");
        $transactionRepository = app(TransactionRepository::class);
        $transaction = $transactionRepository->find($id);

        if (!$transaction) {
            $this->error("Transaction not found.");
            return;
        }

        $callback = json_decode($transaction->payload->raw_callback, true);

        ProcessDarajaPaymentStatusCallback::dispatch($transaction->identifier, $callback)->onQueue('default');

        $this->info("Transaction callback reread dispatched successfully.");
    }
}
