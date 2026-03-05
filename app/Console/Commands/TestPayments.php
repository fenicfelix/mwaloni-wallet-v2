<?php

namespace App\Console\Commands;

use Akika\MoMo\Enums\MtnTargetEnvironment;
use Akika\MoMo\Facades\MoMo;
use Illuminate\Console\Command;

class TestPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Testing MoMo payments...");

        $response = MoMo::with(
            secondaryKey: "9dc1dfaa42cb402cb29a785226405f62",
            userReferenceId: "7aa983ae-7910-4ead-be74-94d62225e33d",
            apiKey: "ab3da92ccee44bac851cf4cd8dafa638",
            targetEnvironment: MtnTargetEnvironment::Ghana,
        )->disbursement()->getAccountBalance();

        $this->info("RESPONSE: " . json_encode($response));
    }
}
