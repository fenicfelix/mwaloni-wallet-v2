<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Models\Account;
use Illuminate\Support\Str;

class CleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-command';

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
        $accounts = Account::all();
        foreach ($accounts as $account) {
            $account->update([
                'identifier' => Str::uuid()->toString(),
            ]);
        }
    }
}
