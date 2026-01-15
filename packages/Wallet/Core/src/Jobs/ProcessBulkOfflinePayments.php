<?php

namespace Wallet\Core\Jobs;

use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBulkOfflinePayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_map('str_getcsv', explode(PHP_EOL, $this->data));
        for ($i = 0; $i < sizeof($data); $i++) {
            try {
                $record = $data[$i];
                $customer_info = explode(" - ", $record[4]);
                $phone_number = $customer_info[0];
                $account_name = $customer_info[1];
                $amount = abs(str_replace(',', '', $record[3]));

                $transaction = Transaction::where('phone_number', 'like', '%' . substr($phone_number, -9))
                    ->where('disbursed_amount', $amount)->first();

                if ($transaction) {
                    if ($transaction->status_id != 2) {
                        $transaction->receipt_number = $record[0];
                        $transaction->account_name = $account_name;
                        $transaction->status_id = 2;
                        $transaction->completed_at = date('Y-m-d H:i:s');

                        if ($transaction->save()) {
                            ProcessOfflinePayments::dispatch($transaction->identifier, $transaction->receipt_number, $transaction->account_name);
                        }
                    }
                } else {
                    throw new \Exception("Transaction not found for phone number: " . $phone_number);
                }
            } catch (\Throwable $th) {
                Log::warning($th->getMessage());
            }
        }
    }
}
