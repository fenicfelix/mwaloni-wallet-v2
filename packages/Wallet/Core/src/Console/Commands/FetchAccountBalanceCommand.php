<?php

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Jobs\Daraja\QueryDarajaBalance;
use Wallet\Core\Jobs\Jenga\QueryJengaBalance;
use Wallet\Core\Jobs\Ncba\QueryNcbaBalance;
use Wallet\Core\Models\Account;
use Wallet\Core\Services\AccountBalanceService;

class FetchAccountBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:fetch-account-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fetch account balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accounts = Account::isActive()->isAutoFetch()->get();
        if ($accounts) {
            foreach ($accounts as $account) {
                app(AccountBalanceService::class)->fetchBalance($account->id);
            }
        }
        return Command::SUCCESS;
    }
}
