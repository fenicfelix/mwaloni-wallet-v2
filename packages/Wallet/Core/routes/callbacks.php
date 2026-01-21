<?php

use Illuminate\Support\Facades\Route;
use Wallet\Core\Http\Controllers\DarajaCallbackController;
use Wallet\Core\Http\Controllers\NCBACallbackController;

// add ncba kplc callback route
Route::post('ncba-kplc-callback/{id}', [NCBACallbackController::class, 'ncba_kplc_callback'])->name("ncba_kplc_callback_url");

// create group route for daraja callback
Route::group(['prefix' => 'drj-callback'], function () {
    Route::match(["get", "post"], 'balance-timeout', [DarajaCallbackController::class, 'balance_timeout'])->name("balance_timeout_url");
    Route::match(["get", "post"], 'balance-result/{id}', [DarajaCallbackController::class, 'balance_result'])->name("balance_result_url");

    Route::match(["get", "post"], 'b2c-timeout', [DarajaCallbackController::class, 'b2c_timeout'])->name("b2c_timeout_url");
    Route::match(["get", "post"], 'b2c-result/{id}', [DarajaCallbackController::class, 'b2c_result'])->name("b2c_result_url");

    Route::match(["get", "post"], 'b2b-timeout', [DarajaCallbackController::class, 'b2b_timeout'])->name("b2b_timeout_url");
    Route::match(["get", "post"], 'b2b-result/{id}', [DarajaCallbackController::class, 'b2b_result'])->name("b2b_result_url");

    Route::match(["get", "post"], 'trx-status-result/{id}', [DarajaCallbackController::class, 'trx_status_result'])->name("trx_status_result_url");
    Route::match(["get", "post"], 'trx-status-timeout', [DarajaCallbackController::class, 'trx_status_timeout'])->name("trx_status_timeout_url");

    Route::match(["get", "post"], 'trx-reversal-result', [DarajaCallbackController::class, 'trx_reversal_result'])->name("trx_reversal_result_url");
    Route::match(["get", "post"], 'trx-reversal-timeout', [DarajaCallbackController::class, 'trx_reversal_timeout'])->name("trx_reversal_timeout_url");
});