<?php

namespace Wallet\Core\Jobs\Ncba;

use Akika\LaravelNcba\Ncba;
use Wallet\Core\Models\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessNcbaPayments implements ShouldQueue
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
        $transaction = Transaction::with(["service", "paymentChannel", "account.currency", "payload"])->where("id", "=", $this->transactionId)->first();
        $transactionRepository = app(TransactionRepository::class);
        try {
            /// Check if the transaction exists
            if (!$transaction) {
                info("Transaction not found");
                return;
            }

            /// Check if the transaction has been submitted
            $result = json_decode($this->submitTransaction($transaction), true);
            if (!$result) {
                info("No response from NCBA API");
                return;
            }

            /// Update the transaction status if the transaction has been submitted
            if ($result["resultCode"] != "000") {
                info("NCBA Payment failed: " . $result["statusDescription"]);
                throw new Exception("Error Processing Request", 1);
            }

            $updateData = [
                "status" => TransactionStatus::SUCCESS,
                "receipt_number" => $result["txnReferenceNo"],
                "result_description" => $result["statusDescription"],
                "completed_at" => now()
            ];
            $payloadData = [
                "raw_callback" => json_encode($result)
            ];

            $transactionRepository->updateWithPayload($transaction->id, $updateData, $payloadData);
            $transactionRepository->completeTransaction($transaction->id);

        } catch (\Throwable $th) {
            info("NCBA Payment Exception: " . $th->getMessage());
            $updateData = [
                "status" => TransactionStatus::FAILED,
                "result_description" => $th->getMessage(),
                "completed_at" => now()
            ];
            $payloadData = [
                "raw_callback" => json_encode($th)
            ];
            $transactionRepository->updateWithPayload($transaction->id, $updateData, $payloadData);
        }
    }

    private function submitTransaction($transaction): ?string
    {
        $account = $transaction->account;
        $ncba = new Ncba($account->consumer_key, $account->api_username, $account->api_password);

        $authenticate = json_decode($ncba->authenticate(), true);
        if (!$authenticate || !isset($authenticate['accessToken'])) {
            info("NCBA Authentication failed");
            return null;
        }

        $response = null;
        $payload = json_decode($transaction->payload?->trx_payload, true);

        // convert $transaction->requested_amount to string
        $amount = (string) $transaction->disbursed_amount;

        switch ($transaction->paymentChannel->slug) {
            case 'ncba-ift':
                $response = $ncba->ift($authenticate['accessToken'], $payload["country"], $transaction->order_number, $transaction->account_number, $transaction->account_name, $account->account_number, $payload["currency"], $amount, $transaction->description);
                break;
            case 'ncba-eft':
                $response = $ncba->eft($authenticate['accessToken'], $amount, $transaction->account_number, $payload["cif"], $transaction->account_name, $account->currency->code, $account->account_number, $transaction->description, $account->country_code, $transaction->order_number, $account->cif);
                break;
            case 'ncba-pesalink':
                $response = $ncba->pesalink($authenticate['accessToken'], $transaction->account_number, $payload["cif"], $transaction->account_name, $amount, $payload["currency"], $transaction->description, $account->account_number, $account->cif, $account->country_name, $transaction->order_number . random_int(1000, 9999));
                break;
            case 'ncba-rtgs':
                $response = $ncba->rtgs($authenticate['accessToken'], $transaction->account_number, $payload["cif"], $payload["country_code"], $transaction->account_name, $payload["address"], $amount, $payload["currency"], $account->currency->code, $transaction->description, $account->account_number, $account->cif, $account->country_code, "FARM", $transaction->order_number . random_int(1000, 9999));
                break;
            case 'ncba-kplc-postpaid':
                $response = $ncba->kplcPostpaidValidation($authenticate['accessToken'], $payload['meter_number'], $payload['msisdn']);
                break;

            default:
                Log::warning("Invalid payment channel");
                break;
        }

        // if $transaction->paymentChannel->slug == 'ncba-kplc-postpaid', send another request to submit the payment here
        if ($transaction->paymentChannel->slug == 'ncba-kplc-postpaid' && isset($response['resultCode']) && $response['resultCode'] == '000') {
            $callbackUrl = route('ncba_kplc_callback_url', $transaction->identifier);
            $response = $ncba->kplcPostpaid($authenticate['accessToken'], $response['validationId'], $response['customerName'], $response['meter_number'], $payload['msisdn'], $transaction->order_number . random_int(1000, 9999), $amount, $callbackUrl, $transaction->account_number);
        }

        return $response;
    }
}
