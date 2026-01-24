<?php

namespace Wallet\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wallet\Core\Jobs\Daraja\ProcessDarajaB2BCallback;
use Wallet\Core\Jobs\Daraja\ProcessDarajaB2CCallback;
use Wallet\Core\Jobs\Daraja\ProcessDarajaPaymentStatusCallback;
use Wallet\Core\Jobs\Daraja\ProcessDarajaReversalCallback;
use Wallet\Core\Models\Account;

class DarajaCallbackController extends Controller
{
    // constructor
    public function __construct()
    {
        // Middleware or other initializations can go here
        info("DarajaCallbackController initialized");
    }

    public function balance_timeout(Request $request)
    {
        $data = $request->json()->all();
    }

    public function balance_result(Request $request, $id)
    {
        $json = $request->json()->all();
        info('Received Balance Callback: ' . json_encode($json));
        if ($json["Result"]["ResultType"] == 0) {
            $account = Account::where("identifier", "=", $id)->first();
            $account->working_balance = getBalance($json["Result"]["ResultParameters"]["ResultParameter"][1]["Value"], "Working Account");
            $account->utility_balance = getBalance($json["Result"]["ResultParameters"]["ResultParameter"][1]["Value"], "Utility Account");
            $account->save();
        }
    }

    public function b2c_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        ProcessDarajaB2CCallback::dispatch($transactionId, $json)->onQueue("b2c-callback");
    }

    public function b2c_timeout(Request $request)
    {
        $json = $request->json()->all();
    }

    public function b2b_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        ProcessDarajaB2BCallback::dispatch($transactionId, $json)->onQueue("b2b-callback");
    }

    public function b2b_timeout(Request $request)
    {
        $json = $request->json()->all();
    }

    public function trx_status_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        ProcessDarajaPaymentStatusCallback::dispatch($transactionId, $json)->onQueue("b2c-callback");
    }

    public function trx_status_timeout(Request $request)
    {
        $json = $request->json()->all();
    }

    public function trx_reversal_result(Request $request)
    {
        $json = $request->json()->all();
        ProcessDarajaReversalCallback::dispatch($json)->onQueue("b2c-callback");
    }

    public function trx_reversal_timeout(Request $request)
    {
        $json = $request->json()->all();
    }
}
