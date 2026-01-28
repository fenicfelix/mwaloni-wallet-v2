<?php

namespace Wallet\Core\Jobs\Ncba;

use Akika\LaravelNcba\Ncba;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Repositories\TransactionRepository;

class QueryNcbaPaymentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = Transaction::with(["service", "account.currency"])->where("id", "=", $this->transactionId)->first();
        if (!$transaction) {
            return;
        }

        $result = $this->performStatusQuery($transaction);
        if (!$result) {
            return;
        }

        if ($result["Code"] !== "000") {
            Log::warning($transaction->id . ": NCBA PAYMENT STATUS: " . json_encode($result));
        }

        $updateData = [
            "status" => TransactionStatus::SUCCESS,
            "receipt_number" => $result["Reference"],
            "result_description" => $result["Description"],
            "completed_at" => date('Y-m-d H:i:s', strtotime($result["Transaction"]["Date"]))
        ];

        $payloadData = [
            "raw_callback" => json_encode($result)
        ];

        $transactionRepository = app(TransactionRepository::class);
        $transactionRepository->updateWithPayload($transaction->id, $updateData, $payloadData);
        $transactionRepository->completeTransaction($transaction->id);
    }

    private function performStatusQuery($transaction): ?array
    {
        $account = $transaction->account;
        $ncba = new Ncba($account->bank_code, $account->branch_code, $account->country_name, $account->currency->code);

        $response = json_decode($ncba->authenticate(), true);

        try {
            if (!$response || !isset($response['accessToken'])) {
                throw new \Exception("Failed to authenticate");
            }

            return $ncba->checkTransactionStatus($response['accessToken'], $account->country_code, $transaction->receipt_number);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
            return null;
        }
    }
}
