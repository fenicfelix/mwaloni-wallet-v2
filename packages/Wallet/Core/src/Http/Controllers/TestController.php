<?php

namespace Wallet\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\MwaloniWallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Wallet\Core\Models\Account;

class TestController extends Controller
{
    use MwaloniWallet;
    
    public function index($module)
    {

        if ($module == "home") {
            $this->data["title"] = "TESTS - " . config('app.name');
            $this->data["content_title"] = "Tests Manager";
            return view('pages.tests.index', $this->data);
        } else {
            return $this->$module();
        }
    }

    private function balance()
    {
        $account = Account::where("id", 2)->first();
        $result = json_decode(jenga_fetch_balance($account));
        echo json_encode($result) . "</br>";
        if ($result->status) {
            $account->working_balance = $result->data->balances[0]->amount;
            $account->utility_balance = $result->data->balances[1]->amount;
            if ($account->save()) echo "Balance processed successfully";
            else echo "Could not save balance";
        } else {
            echo "Error processing balance";
        }
    }

    private function mini_statement()
    {
        $account = Account::where("id", 7)->first();
        echo jenga_get_mini_statement($account);
        //{"status":true,"code":0,"message":"success","data":{"balance":4.91852128E8,"currency":"KES","accountNumber":"1450160649886","transactions":[{"date":"2022-12-14T00:00:00.000","amount":"31800","description":"IFT637437428081","chequeNumber":null,"type":"Debit"},{"date":"2022-12-14T00:00:00.000","amount":"86600","description":"IFT658506433644","chequeNumber":null,"type":"Debit"},{"date":"2022-12-14T00:00:00.000","amount":"110","description":"MPESA PAYMENT TO 254725036415","chequeNumber":null,"type":"Debit"},{"date":"2022-12-14T00:00:00.000","amount":"32","description":"JENGA CHARGE CREDIT 667912538842","chequeNumber":null,"type":"Credit"},{"date":"2022-12-14T00:00:00.000","amount":"32","description":"JENGA CHARGE DEBIT 667912538842","chequeNumber":null,"type":"Debit"},{"date":"2022-12-14T00:00:00.000","amount":"100","description":"MPESA PAYMENT TO 254725036415","chequeNumber":null,"type":"Debit"},{"date":"2022-12-14T00:00:00.000","amount":"20","description":"JENGA CHARGE CREDIT 667912412399","chequeNumber":null,"type":"Credit"},{"date":"2022-12-14T00:00:00.000","amount":"20","description":"JENGA CHARGE DEBIT 667912412399","chequeNumber":null,"type":"Debit"},{"date":"2022-12-13T00:00:00.000","amount":"1","description":"JENGA CHARGE CREDIT 670930722175155","chequeNumber":null,"type":"Credit"},{"date":"2022-12-13T00:00:00.000","amount":"1","description":"JENGA CHARGE DEBIT 670930722175155","chequeNumber":null,"type":"Debit"}]}}
    }

    private function full_statement()
    {
        $account = Account::where("id", 7)->first();
        echo jenga_get_full_statement($account, '2022-01-01', date('Y-m-d'), 20);
        //{"status":true,"code":0,"message":"success","data":{"balance":4.91852128E8,"currency":"KES","accountNumber":"1450160649886","transactions":[{"reference":"637437428081","date":"2022-12-14T00:00:00.000","amount":31800,"serial":"1","description":"IFT637437428081","postedDateTime":"2022-11-14T12:49:25.000","type":"Debit","runningBalance":{"amount":344957.3,"currency":"KES"},"transactionId":"5417"},{"reference":"658506433644","date":"2022-12-14T00:00:00.000","amount":86600,"serial":"2","description":"IFT658506433644","postedDateTime":"2022-11-14T12:49:14.000","type":"Debit","runningBalance":{"amount":258357.3,"currency":"KES"},"transactionId":"5416"},{"reference":"mI7rJQw9Nxx0","date":"2022-12-14T00:00:00.000","amount":110,"serial":"3","description":"MPESA PAYMENT TO 254725036415","postedDateTime":"2022-11-08T16:02:24.000","type":"Debit","runningBalance":{"amount":258247.3,"currency":"KES"},"transactionId":"5411"},{"reference":"667912538842","date":"2022-12-14T00:00:00.000","amount":32,"serial":"4","description":"JENGA CHARGE CREDIT 667912538842","postedDateTime":"2022-11-08T16:02:20.000","type":"Credit","runningBalance":{"amount":258279.3,"currency":"KES"},"transactionId":"5410"},{"reference":"667912538842","date":"2022-12-14T00:00:00.000","amount":32,"serial":"5","description":"JENGA CHARGE DEBIT 667912538842","postedDateTime":"2022-11-08T16:02:20.000","type":"Debit","runningBalance":{"amount":258247.3,"currency":"KES"},"transactionId":"5410"},{"reference":"4F7WUMaLGHd1","date":"2022-12-14T00:00:00.000","amount":100,"serial":"6","description":"MPESA PAYMENT TO 254725036415","postedDateTime":"2022-11-08T16:00:31.000","type":"Debit","runningBalance":{"amount":258147.3,"currency":"KES"},"transactionId":"549"},{"reference":"667912412399","date":"2022-12-14T00:00:00.000","amount":20,"serial":"7","description":"JENGA CHARGE CREDIT 667912412399","postedDateTime":"2022-11-08T16:00:20.000","type":"Credit","runningBalance":{"amount":258167.3,"currency":"KES"},"transactionId":"548"},{"reference":"667912412399","date":"2022-12-14T00:00:00.000","amount":20,"serial":"8","description":"JENGA CHARGE DEBIT 667912412399","postedDateTime":"2022-11-08T16:00:20.000","type":"Debit","runningBalance":{"amount":258147.3,"currency":"KES"},"transactionId":"548"},{"reference":"670930669024727","date":"2022-12-13T00:00:00.000","amount":1,"serial":"9","description":"JENGA CHARGE CREDIT 670930669024727","postedDateTime":"2022-12-13T14:24:45.000","type":"Credit","runningBalance":{"amount":258148.3,"currency":"KES"},"transactionId":"54922"},{"reference":"670930669024727","date":"2022-12-13T00:00:00.000","amount":1,"serial":"10","description":"JENGA CHARGE DEBIT 670930669024727","postedDateTime":"2022-12-13T14:24:45.000","type":"Debit","runningBalance":{"amount":258147.3,"currency":"KES"},"transactionId":"54922"},{"reference":"670930332317868","date":"2022-12-13T00:00:00.000","amount":1,"serial":"11","description":"JENGA CHARGE CREDIT 670930332317868","postedDateTime":"2022-12-13T14:18:54.000","type":"Credit","runningBalance":{"amount":258148.3,"currency":"KES"},"transactionId":"54921"},{"reference":"670930332317868","date":"2022-12-13T00:00:00.000","amount":1,"serial":"12","description":"JENGA CHARGE DEBIT 670930332317868","postedDateTime":"2022-12-13T14:18:54.000","type":"Debit","runningBalance":{"amount":258147.3,"currency":"KES"},"transactionId":"54921"},{"reference":"692149225782","date":"2022-12-13T00:00:00.000","amount":19,"serial":"13","description":"IFT692149225782","postedDateTime":"2022-12-13T14:13:52.000","type":"Debit","runningBalance":{"amount":258128.3,"currency":"KES"},"transactionId":"54920"},{"reference":"670930008922416","date":"2022-12-13T00:00:00.000","amount":1,"serial":"14","description":"JENGA CHARGE CREDIT 670930008922416","postedDateTime":"2022-12-13T14:13:48.000","type":"Credit","runningBalance":{"amount":258129.3,"currency":"KES"},"transactionId":"54919"},{"reference":"670930008922416","date":"2022-12-13T00:00:00.000","amount":1,"serial":"15","description":"JENGA CHARGE DEBIT 670930008922416","postedDateTime":"2022-12-13T14:13:47.000","type":"Debit","runningBalance":{"amount":258128.3,"currency":"KES"},"transactionId":"54919"},{"reference":"670929592516744","date":"2022-12-13T00:00:00.000","amount":1,"serial":"16","description":"JENGA CHARGE CREDIT 670929592516744","postedDateTime":"2022-12-13T14:06:59.000","type":"Credit","runningBalance":{"amount":258129.3,"currency":"KES"},"transactionId":"54628"},{"reference":"670929592516744","date":"2022-12-13T00:00:00.000","amount":1,"serial":"17","description":"JENGA CHARGE DEBIT 670929592516744","postedDateTime":"2022-12-13T14:06:59.000","type":"Debit","runningBalance":{"amount":258128.3,"currency":"KES"},"transactionId":"54628"},{"reference":"670928925025229","date":"2022-12-13T00:00:00.000","amount":1,"serial":"18","description":"JENGA CHARGE CREDIT 670928925025229","postedDateTime":"2022-12-13T13:55:29.000","type":"Credit","runningBalance":{"amount":258129.3,"currency":"KES"},"transactionId":"54347"},{"reference":"670928925025229","date":"2022-12-13T00:00:00.000","amount":1,"serial":"19","description":"JENGA CHARGE DEBIT 670928925025229","postedDateTime":"2022-12-13T13:55:29.000","type":"Debit","runningBalance":{"amount":258128.3,"currency":"KES"},"transactionId":"54347"},{"reference":"670928890355197","date":"2022-12-13T00:00:00.000","amount":1,"serial":"20","description":"JENGA CHARGE CREDIT 670928890355197","postedDateTime":"2022-12-13T13:55:10.000","type":"Credit","runningBalance":{"amount":258129.3,"currency":"KES"},"transactionId":"54346"}]}}
    }

    private function send_to_equity()
    {

        $account = Account::where("id", 7)->first();
        $destination = [
            "countryCode" => "KE",
            "accountName" => "Felix Ogucha",
            "accountNumber" => "0710199473352",
            "amount" => "20",
            "currencyCode" => "KES",
            "reference" => "TS92149225782",
            "description" => "Monthly payments",
        ];
        $result = jenga_send_to_equity($account, $destination);
        info($result);
        echo $result;
        //{"status":true,"code":0,"message":"success","reference":"692149225782","data":{"transactionId":"54920","status":"SUCCESS"}}
    }

    private function send_to_mpesa()
    {
        $account = Account::where("id", 7)->first();
        $destination = [
            "countryCode" => "KE",
            "accountName" => "Felix Ogucha",
            "mobileNumber" => "0723293349",
            "walletName" => "Mpesa",
            "amount" => "100",
            "currencyCode" => "KES",
            "description" => "Monthly payments",
            "reference" => "100234567890"
        ];
        $result = jenga_send_to_mobile($account, $destination);
        info($result);
        echo $result;
    }

    private function send_to_equitel()
    {
        $account = Account::where("id", 7)->first();
        $destination = [
            "countryCode" => "KE",
            "accountName" => "Felix Ogucha",
            "mobileNumber" => "0763000123",
            "walletName" => "Equitel",
            "amount" => "100",
            "currencyCode" => "KES",
            "description" => "Monthly payments",
            "reference" => "100234567890"
        ];
        $result = jenga_send_to_mobile($account, $destination);
        info($result);
        echo $result;
    }

    private function rtgs()
    {
        //jenga_send_rtgs
        $account = Account::with(["currency"])->where("id", 7)->first();
        $destination = [
            "countryCode" => "KE",
            "accountName" => "Felix Ogucha",
            "bankCode" => "01",
            "accountNumber" => "0723293349",
            "amount" => "10",
            "currencyCode" => "KES",
            "reference" => "DIOL692194625798",
            "description" => "Monthly payments",
            "date" => date("Y-m-d")
        ];
        $result = jenga_send_rtgs($account, $destination);
        info($result);
        echo $result;
        //{"status":true,"code":0,"message":"success","reference":"DIOL692194625798","data":{"transactionId":"DIOL692194625798","status":"SUCCESS"}}
    }

    private function swift()
    {
        //jenga_send_rtgs
        $account = Account::with(["currency"])->where("id", 2)->first();
        $destination = [
            "countryCode" => "KE",
            "accountName" => "Felix Ogucha",
            "bankBic" => "01",
            "accountNumber" => "0723293349",
            "addressline1" => "Post Box 56",
            "amount" => "10",
            "currencyCode" => "USD", //Transfers to different currency
            "reference" => "DIO692194625701",
            "description" => "Monthly payments",
            "date" => date('Y-m-d'),
            "chargeOption" => "SELF"
        ];
        $result = jenga_send_swift($account, $destination);
        info($result);
        echo $result;
        //{"status":true,"code":0,"message":"success","reference":"DIOL692194625798","data":{"transactionId":"DIOL692194625798","status":"SUCCESS"}}
    }

    private function pesalink_bank()
    {
        $destination = [
            "countryCode" => "KE",
            "accountName" => "Felix Ogucha",
            "mobileNumber" => "0723293349",
            "bankCode" => "01",
            "accountNumber" => "0723293349",
            "amount" => "150", //Should be > 100
            "currencyCode" => "KES",
            "reference" => "123456789019", //Only numeric of length 12 allowed for `transfer.reference` 
            "date" => date('Y-m-d'),
            "description" => NULL,
        ];
        $account = Account::with(["currency"])->where("id", 2)->first();
        $result = jenga_pesalink_bank($account, $destination);
        info($result);
        echo $result;
    }

    private function pesalink_mobile()
    {
        $destination = [
            "countryCode" => "KE",
            "accountName" => "Felix Ogucha",
            "mobileNumber" => "0723293349",
            "bankCode" => "01",
            "amount" => "150",
            "currencyCode" => "KES",
            "reference" => "123456789004",
        ];
        $account = Account::with(["currency"])->where("id", 2)->first();
        $result = jenga_pesalink_mobile($account, $destination);
        info($result);
        echo $result;
        //{"status":true,"code":0,"message":"success","reference":"123456789001","data":{"description":"Confirmed. Ksh 150.0 sent to 0723293349- Felix Ogucha from your account 1450160649886 on Tue Dec 13 15:29:12 EAT 2022. Ref 123456789001. Thank you","transactionId":"123456789001","status":"SUCCESS"}}
    }

    public function test_sms(Request $request)
    {
        if (!$request->get('phone')) return response()->json(['status' => status_error, 'message' => "Missing Phone Number."], Response::HTTP_OK);

        $message = "Hello test message from WWT " . date('d M, Y H:i:s');
        $to = clean_phone_number($request->get('phone'));

        $this->send_sms($to, $message);

        return response()->json(['status' => status_success, 'message' => "Message Scheculed."], Response::HTTP_OK);
    }
}
