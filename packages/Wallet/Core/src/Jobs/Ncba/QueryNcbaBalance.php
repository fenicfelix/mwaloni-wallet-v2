<?php

namespace Wallet\Core\Jobs\Ncba;

use Akika\LaravelNcba\Ncba;
use Wallet\Core\Models\Account;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class QueryNcbaBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $accountId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $account = Account::where("id", $this->accountId)->first();
        if ($account) {
            $ncba = new Ncba($account->consumer_key, $account->api_username, $account->api_password);
            $authenticate = json_decode($ncba->authenticate(), true);
            try {
                if (!$authenticate || !isset($authenticate['accessToken'])) {
                    throw new Exception("Authentication problem.", 1);
                    return;
                }

                $balance = $ncba->accountDetails($authenticate['accessToken'], $account->country_code, $account->account_number);
                if ($balance['ErrorCode'] == "000") {
                    $account->update([
                        'working_balance' => 0,
                        'utility_balance' => $balance['AvailableBalance']
                    ]);
                }
            } catch (\Throwable $th) {
                Log::warning("NCBA Error: " . $th->getMessage());
            }
        }
    }
}
