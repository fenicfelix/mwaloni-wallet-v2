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
    public function balance_timeout(Request $request)
    {
        $data = $request->json()->all();
        return response()->json(['status' => 'success'], 200);
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
        return response()->json(['status' => 'success'], 200);
    }

    public function b2c_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        ProcessDarajaB2CCallback::dispatch($transactionId, $json)->onQueue("b2c-callback");
        return response()->json(['status' => 'success'] , 200);
    }

    public function b2c_timeout(Request $request)
    {
        $json = $request->json()->all();
        return response()->json(['status' => 'success'], 200);
    }

    public function b2b_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        ProcessDarajaB2BCallback::dispatch($transactionId, $json)->onQueue("b2b-callback");
        return response()->json(['status' => 'success'], 200);
    }

    public function b2b_timeout(Request $request)
    {
        $json = $request->json()->all();
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_status_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        ProcessDarajaPaymentStatusCallback::dispatch($transactionId, $json)->onQueue("b2c-callback");
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_status_timeout(Request $request)
    {
        $json = $request->json()->all();
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_reversal_result(Request $request)
    {
        $json = $request->json()->all();
        ProcessDarajaReversalCallback::dispatch($json)->onQueue("b2c-callback");
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_reversal_timeout(Request $request)
    {
        $json = $request->json()->all();
        return response()->json(['status' => 'success'], 200);
    }
}
