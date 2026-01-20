<?php

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;
use Wallet\Core\Jobs\PopulateTransactionMetricTable;

class PopulateTransactionMetricTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:populate-transaction-metric';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the transaction_metric table with initial data';

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
        // Populate the transaction_metric table with initial data
        $this->info('Populating transaction_metric table...');
        // You can use a factory or create instances manually
        PopulateTransactionMetricTable::dispatch()->onQueue('default');

        return 0;
    }
}
