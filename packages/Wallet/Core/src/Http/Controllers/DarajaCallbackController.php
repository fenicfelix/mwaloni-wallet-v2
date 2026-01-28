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
        info('Balance Callback: ' . json_encode($json));
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
        info('B2C Callback: ' . json_encode($json));
        ProcessDarajaB2CCallback::dispatch($transactionId, $json)->onQueue("b2c-callback");
        return response()->json(['status' => 'success'] , 200);
    }

    public function b2c_timeout(Request $request)
    {
        $json = $request->json()->all();
        info('B2C Timeout Callback: ' . json_encode($json));
        return response()->json(['status' => 'success'], 200);
    }

    public function b2b_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        info('B2b Callback: ' . json_encode($json));
        ProcessDarajaB2BCallback::dispatch($transactionId, $json)->onQueue("b2b-callback");
        return response()->json(['status' => 'success'], 200);
    }

    public function b2b_timeout(Request $request)
    {
        $json = $request->json()->all();
        info('B2b Timeout Callback: ' . json_encode($json));
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_status_result(Request $request, $transactionId)
    {
        $json = $request->json()->all();
        info('Transaction Status Callback: ' . json_encode($json));
        ProcessDarajaPaymentStatusCallback::dispatch($transactionId, $json)->onQueue("b2c-callback");
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_status_timeout(Request $request)
    {
        $json = $request->json()->all();
        info('Transaction Status Timeout Callback: ' . json_encode($json));
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_reversal_result(Request $request)
    {
        $json = $request->json()->all();
        info('Transaction Reversal Callback: ' . json_encode($json));
        ProcessDarajaReversalCallback::dispatch($json)->onQueue("b2c-callback");
        return response()->json(['status' => 'success'], 200);
    }

    public function trx_reversal_timeout(Request $request)
    {
        $json = $request->json()->all();
        info('Transaction Reversal Timeout Callback: ' . json_encode($json));
        return response()->json(['status' => 'success'], 200);
    }
}
