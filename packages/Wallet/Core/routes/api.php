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

Route::get('test', function () {
    return response()->json(["status" => "00", "message" => "Success"], 200);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('authenticate', [AuthController::class, 'authenticate']);

// Route::middleware('auth:sanctum')->group(function () {
    Route::post('get-balance', [ApiController::class, 'fetchBalance']);
    Route::post('get-transaction-status', [ApiController::class, 'getTransactionStatus']);
    Route::post('send-money', [ApiController::class, 'sendMoney']);
    Route::post('contact-lookup', [ApiController::class, 'contactLookup']);
    Route::post('send-sms', [ApiController::class, 'clientSendSMS']);
// });