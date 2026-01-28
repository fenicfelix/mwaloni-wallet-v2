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

        $this->performBalanceQuery($account);
    }

    private function performBalanceQuery($account): ?string
    {
        $mpesa = new Mpesa(
            $account->account_number,
            $account->consumer_key,
            $account->consumer_secret,
            $account->api_username,
            $account->api_password
        );
        
        $response = $mpesa->getBalance(route('balance_result_url', $account->identifier), route('balance_timeout_url'));

        return $response;
    }
}
