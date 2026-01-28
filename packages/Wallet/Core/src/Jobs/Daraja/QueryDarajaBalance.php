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

class QueryDarajaBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $accountId) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $account = Account::find($this->accountId);
        if (!$account) {
            // Handle account not found
            return;
        }

        $mpesaShortCode = $account->account_number;
        $consumerKey = $account->consumer_key;
        $consumerSecret = $account->consumer_secret;
        $apiUsername = $account->api_username;
        $apiPassword = $account->api_password;

        $this->performBalanceQuery(
            $account,
            $mpesaShortCode,
            $consumerKey,
            $consumerSecret,
            $apiUsername,
            $apiPassword
        );
    }

    private function performBalanceQuery($account, $mpesaShortCode, $consumerKey, $consumerSecret, $apiUsername, $apiPassword): ?string
    {
        $mpesa = new Mpesa($mpesaShortCode, $consumerKey, $consumerSecret, $apiUsername, $apiPassword);
        $response = $mpesa->getBalance(route('balance_result_url', $account->identifier), route('balance_timeout_url'));

        info('BALANCE_RESPONSE: ' . $response);
        return $response;
    }
}
