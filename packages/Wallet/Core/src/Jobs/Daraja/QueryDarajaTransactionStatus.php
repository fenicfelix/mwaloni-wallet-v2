<?php

namespace Wallet\Core\Jobs\Daraja;

use Akika\LaravelMpesaMultivendor\Mpesa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Models\Account;
use Wallet\Core\Models\Transaction;

class QueryDarajaTransactionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $transactionId) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = Transaction::with(['account', 'payload'])->find($this->transactionId);
        if (!$transaction) {
            // Handle account not found
            return;
        }

        $account = $transaction->account;
        $mpesaShortCode = $account->account_number;
        $consumerKey = $account->consumer_key;
        $consumerSecret = $account->consumer_secret;
        $apiUsername = $account->api_username;
        $apiPassword = $account->api_password;
        $mpesa = new Mpesa($mpesaShortCode, $consumerKey, $consumerSecret, $apiUsername, $apiPassword);
        return $mpesa->getTransactionStatus($transaction->receipt_number, "shortcode", $transaction->description, route('trx_status_result_url', $transaction->identifier), route('trx_status_timeout_url'), $transaction->payload?->original_conversation_id);
    }
}
