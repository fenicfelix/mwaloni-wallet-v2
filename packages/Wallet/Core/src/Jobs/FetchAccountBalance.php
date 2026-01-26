<?php

namespace Wallet\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Jobs\Daraja\QueryDarajaBalance;
use Wallet\Core\Jobs\Jenga\QueryJengaBalance;
use Wallet\Core\Models\Account;

class FetchAccountBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Account $account) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->account->account_type_id == 1) { //Daraja
            QueryDarajaBalance::dispatch($this->account->id);
        } else if ($this->account->account_type_id == 2) { //Jenga
            QueryJengaBalance::dispatch($this->account->id);
        } else if ($this->account->account_type_id == 3) { //NCBA
            // QueryNcbaBalance::dispatch($this->account->id);
        }

        return true;
    }
}
