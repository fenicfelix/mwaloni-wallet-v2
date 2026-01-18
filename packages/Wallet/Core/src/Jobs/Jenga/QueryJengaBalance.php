<?php

namespace Wallet\Core\Jobs\Jenga;

use Wallet\Core\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Jobs\Jenga;

class QueryJengaBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Jenga;

    protected $accountId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $account = Account::where("id", $this->accountId)->first();
            if ($account) {
                $result = json_decode($this->jenga_fetch_balance($account));
                if ($result->status) {
                    $account->working_balance = 0;
                    foreach ($result->data->balances as $balance) {
                        if ($balance->type == "Current") $account->utility_balance = $balance->amount;
                    }
                    
                    if (!$account->save()) throw new \Exception("An error ocurred while processing balance.");
                } else {
                    throw new \Exception("Balance enquiry not made successfully.");
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::warning($th->getMessage());
        }
    }
}
