<?php

namespace Wallet\Core\Jobs\Momo;

use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Facades\MoMo;
use Wallet\Core\Models\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Repositories\TransactionRepository;
use Wallet\Core\Services\ProcessMomoStatus;

class ProcessMomoCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    protected $transactionId;
    protected $json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $json)
    {
        $this->transactionId = $transactionId;
        $this->json = $json;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        return app(ProcessMomoStatus::class)->process($this->transactionId, $this->json);
    }
}
