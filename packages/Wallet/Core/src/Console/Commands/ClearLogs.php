<?php

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to clear logs files to free up disk space';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Deleting all log files to free up disk space!');
        shell_exec('rm ' . storage_path('logs') . '/*');
        $this->info('SUCCESS! The log files have been deleted successfully');
        return Command::SUCCESS;
    }
}
