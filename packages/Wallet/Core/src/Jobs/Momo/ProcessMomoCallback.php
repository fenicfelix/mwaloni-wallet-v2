<?php

namespace Wallet\Core\Jobs\Momo;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Services\ProcessMomoStatus;

class ProcessMomoCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    protected $json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($json)
    {
        $this->json = $json;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        return app(ProcessMomoStatus::class)->process($this->json);
    }
}
