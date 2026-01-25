<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Models\Transaction;

class WalletCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wallet-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up wallet data...');
        // // Perform cleanup tasks here
        // // get all transactions where order_number = REV0001
        // $order_number = 'REV0001';
        // $transactions = Transaction::where('order_number', $order_number)->get();
        // $counter = 0;
        // foreach ($transactions as $transaction) {
        //     $order_number = 'REV' . str_pad(++$counter, 4, '0', STR_PAD_LEFT);
        //     $transaction->order_number = $order_number;
        //     $transaction->save();
        //     $this->info('TRX_id: ' . $transaction->id. ' - ' . $order_number);
        // }

        // // update transactions set transaction_type='service_charge' where order_number like 'SR%';
        // Transaction::where('order_number', 'like', 'SRVC%')
        //     ->update(['transaction_type' => 'service_charge']);

        $this->info('Wallet cleanup completed.');
    }
}
