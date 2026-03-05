<?php

namespace Wallet\Core\Jobs\Momo;

use Akika\MoMo\Enums\MtnTargetEnvironment;
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
            secondaryKey: "9dc1dfaa42cb402cb29a785226405f62",
            userReferenceId: "7aa983ae-7910-4ead-be74-94d62225e33d",
            apiKey: "ab3da92ccee44bac851cf4cd8dafa638",
            targetEnvironment: MtnTargetEnvironment::Ghana,
        )->disbursement()->getAccountBalance();
    }
}
