<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Wallet\Core\Http\Controllers\ApiController;
use Wallet\Core\Http\Controllers\AuthController;
use Wallet\Core\Http\Controllers\DarajaCallbackController;
use Wallet\Core\Http\Controllers\NCBACallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test', function () {
    return response()->json(["status" => "00", "message" => "Success"], 200);
});

Route::post('authenticate', [AuthController::class, 'authenticate']);

// Version 2 routes
Route::prefix('api')->group(function () {
    Route::post('get-balance', [ApiController::class, 'fetchBalance']);
    Route::post('get-transaction-status', [ApiController::class, 'getTransactionStatus']);
    Route::post('send-money', [ApiController::class, 'sendMoney']);
    Route::post('contact-lookup', [ApiController::class, 'contactLookup']);
    Route::post('send-sms', [ApiController::class, 'clientSendSMS']);
});

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
