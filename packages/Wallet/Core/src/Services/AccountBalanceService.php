<?php

namespace Wallet\Core\Services;

use Wallet\Core\Jobs\Daraja\QueryDarajaBalance;
use Wallet\Core\Jobs\FetchAccountBalance;
use Wallet\Core\Jobs\Jenga\QueryJengaBalance;
use Wallet\Core\Jobs\Ncba\QueryNcbaBalance;
use Wallet\Core\Repositories\AccountRepository;

class AccountBalanceService
{
    // Account balance service methods go here
    public function fetchBalance($accountId): bool
    {
        // Logic to fetch account balance
        try {
            $account = app(AccountRepository::class)->find($accountId);
            if(!$account) return false;

            if ($account->account_type_id == 1) { //Daraja
                info('Fetching balance for daraja account');
                QueryDarajaBalance::dispatch($account->id)->onQueue("fetch-balance");
            } else if ($account->account_type_id == 2) { //Jenga
                info('Fetching balance for jenga account');
                QueryJengaBalance::dispatch($account->id)->onQueue("fetch-balance");
            } else {
                info('Fetching balance for ncba account');
                QueryNcbaBalance::dispatch($account->id)->onQueue("fetch-balance");
            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
            return false;
        }
    }
}
