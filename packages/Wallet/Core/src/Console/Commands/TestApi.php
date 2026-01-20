<?php

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class TestApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test API endpoint';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        $this->info('Testing API...');
        
        $username = 'juliusci4';
        $password = 'Qfob83EtVp';
        $apiKey = '88f54950051c0b0ea5afd1ac45397e53ef8343e307aa51d39ce3759fda866f80';

        // $balance = 

        return Command::SUCCESS;
    }
}