<?php

namespace Wallet\Core\Jobs\Momo;

use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Enums\MtnTargetEnvironment;
use Akika\MoMo\Facades\MoMo;
use Wallet\Core\Models\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Repositories\TransactionRepository;
use Wallet\Core\Services\ProcessMomoStatus;

class QueryMomoPaymentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        $transaction = Transaction::with(["account"])->where("id", "=", $this->transactionId)->first();
        if (!$transaction) {
            return;
        }

        $account = $transaction->account;

        $status = MoMo::with(
            secondaryKey: "9dc1dfaa42cb402cb29a785226405f62",
            userReferenceId: "7aa983ae-7910-4ead-be74-94d62225e33d",
            apiKey: "ab3da92ccee44bac851cf4cd8dafa638",
            targetEnvironment: MtnTargetEnvironment::Ghana,
        )->disbursement()->getTransferStatus($this->transactionId);

        return app(ProcessMomoStatus::class)->process($status);
    }
}
