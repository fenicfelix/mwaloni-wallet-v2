<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Wallet\Core\Console\Commands\ClearLogs;
use Wallet\Core\Console\Commands\FetchAccountBalanceCommand;
use Wallet\Core\Console\Commands\PopulateTransactionMetricTableCommand;
use Wallet\Core\Console\Commands\ProcessPendingPayments;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Core package commands
Schedule::command(ClearLogs::class)->weekly();
Schedule::command(FetchAccountBalanceCommand::class)
    ->everyFiveMinutes()
    ->appendOutputTo(storage_path() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'fetch-account-balance.log');

Schedule::command('stanbic:read')->everyFiveMinutes();

Schedule::command(PopulateTransactionMetricTableCommand::class)->everyTenMinutes()
    ->appendOutputTo(storage_path() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'metrics.log');

Schedule::command(ProcessPendingPayments::class)->everyMinute();
