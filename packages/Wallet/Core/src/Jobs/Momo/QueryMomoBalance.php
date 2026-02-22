<?php

namespace Wallet\Core\Jobs\Momo;

use Akika\MoMo\Facades\MoMo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Models\Account;

class QueryMomoBalance implements ShouldQueue
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

        $balanceData = $this->fetchBalance($account);
        $account->utility_balance = $balanceData['availableBalance'] ?? 0;
        $account->save();
    }

    private function fetchBalance(Account $account): array
    {
        return MoMo::with(
            $account->consumer_secret,  // overrides momo.<env>.secondary_key
            $account->api_username,     // overrides momo.<env>.user_reference_id
            $account->api_password,
        )->disbursement()->getAccountBalance();
    }
}
