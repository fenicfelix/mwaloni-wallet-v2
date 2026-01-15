<?php

namespace Wallet\Core\Jobs;

use App\Jobs\Jenga\QueryJengaBalance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchAccountBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    protected $account;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = true;
        if ($this->account->account_type_id == 1) { //Daraja
            $result = (object) json_decode($this->daraja_account_balance($this->account));
            info('Daraja balance result: ' . json_encode($result));
            try {
                if ($result->ResponseCode != "0") {
                    $result = false;
                }
            } catch (\Throwable $th) {
                Log::warning($th->getMessage());
                $result = false;
            }
        } else if ($this->account->account_type_id == 2) { //Jenga
            QueryJengaBalance::dispatch($this->account->id);
        }
    }
}
