<?php

namespace Wallet\Core\Http\Traits;

use Akika\LaravelMpesaMultivendor\Mpesa;
use App\Jobs\ProcessSMS;
use Wallet\Core\Models\Outbox;
use Wallet\Core\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait MwaloniWallet
{
    private function generate_reference($prefix = "FK")
    {
        $reference = $prefix . date('ymdHis') . rand(1000, 9999);
        return $reference;
    }

    private function generate_payload($channel, $data, $amount)
    {
        // if $channel like ncba-ift, ncba-*
        $payload = [];
        if (strpos($channel->slug, 'ncba') !== false) {
            $payload = [
                "bank" => $data['bank_name'] ?? '',
                "cif" => $data['bank_cif'] ?? '',
                "country" => $data['country'] ?? 'Kenya',
                "country_code" => $data['country_code'] ?? 'KE',
                "address" => $data['address'] ?? 'Nairobi',
                "currency" => $data['currency_code'] ?? 'KES',
                "reference" => date('ymdHs') . rand(0, 99),
                "msisdn" => $data['msisdn'] ?? '',
                "meter_number" => $data['meter_number'] ?? '',
            ];
        } else if ($channel->slug == 'stanbic') {
            $body['order_number'] = preg_replace('/[^A-Za-z0-9]/', '', $data['order_number']);

            $payload = [
                'messageId' => $body['order_number'],
                'creditorBankCode' => $data['creditorBankCode'],
                'creditorBankName' => $data['creditorBankName'],
                'beneficiaryName' => $data['account_name'],
                'beneficiaryAcNo' => $data['account_number'],
                'paymentInfoId' => 'PMTINF0' . $body['order_number'],
                'paymentId' => 'PMT0' . $body['order_number'],
                'instructionId' => 'INST0' . $body['order_number'],
                'amount' => $data['amount'],
                'paymentDescription' => $data['description'],
            ];
        } else {
            switch ($channel->slug) {
                case 'daraja-mobile':
                    $payload = [
                        "countryCode" => $data['country_code'] ?? 'KE',
                        "accountName" => $data['account_name'] ?? NULL,
                        "mobileNumber" => $data['account_number'],
                        "walletName" => "Mpesa",
                        "amount" => $amount,
                        "currencyCode" => $data['currency_code'] ?? 'KES',
                        "description" => $data['description'] ?? 'Cashout',
                        "reference" => date('ymdHs') . rand(0, 99)
                    ];
                    break;

                case 'daraja-paybill':
                    $payload = [
                        "countryCode" => $data['country_code'] ?? 'KE',
                        "accountName" => cleanAccountName($data['account_name']),
                        "mobileNumber" => $data['account_number'],
                        "walletName" => "Mpesa",
                        "amount" => $amount,
                        "currencyCode" => $data['currency_code'] ?? 'KES',
                        "description" => $data['description'] ?? 'Cashout',
                        "reference" => date('ymdHs') . rand(0, 99)
                    ];
                    break;

                case 'daraja-till':
                    $payload = [
                        "countryCode" => $data['country_code'] ?? 'KE',
                        "accountName" => cleanAccountName($data['account_name']),
                        "mobileNumber" => $data['account_number'],
                        "walletName" => "Mpesa",
                        "amount" => $amount,
                        "currencyCode" => $data['currency_code'] ?? 'KES',
                        "description" => $data['description'] ?? 'Cashout',
                        "reference" => date('ymdHs') . rand(0, 99)
                    ];
                    break;

                case 'jenga-pesalink-bank':
                    $payload = [
                        "countryCode" => "KE",
                        "accountName" => cleanAccountName($data['account_name']),
                        "bankCode" => $data['bank_code'] ?? '01',
                        "accountNumber" => $data["account_number"],
                        "amount" => $amount,
                        "currencyCode" => $data['currency_code'] ?? 'KES',
                        "description" => $data['description'] ?? 'Cashout',
                        "reference" => date('ymdHs') . rand(0, 99),
                        "date" => date("Y-m-d")
                    ];
                    break;
                case 'jenga-pesalink-mobile':
                    $payload = [
                        "countryCode" => "KE",
                        "accountName" => cleanAccountName($data['account_name']),
                        "bankCode" => $data['bank_code'] ?? '01',
                        "accountNumber" => $data["account_number"],
                        "amount" => $amount,
                        "currencyCode" => $data['currency_code'] ?? 'KES',
                        "description" => $data['description'] ?? 'Cashout',
                        "reference" => date('ymdHs') . rand(0, 99),
                        "date" => date("Y-m-d")
                    ];
                    break;
                case 'jenga-ift':
                    $payload = [
                        "countryCode" => "KE",
                        "accountName" => cleanAccountName($data['account_name']),
                        "accountNumber" => $data["account_number"],
                        "amount" => $amount,
                        "currencyCode" => $data['currency_code'] ?? 'KES',
                        "description" => $data['description'] ?? 'Cashout',
                        "reference" => date('ymdHs') . rand(0, 99),
                        "date" => date("Y-m-d")
                    ];
                    break;
                default:
                    # code...
                    break;
            }
        }

        return $payload;
    }

    private function authenticate_service(Request $request)
    {
        $password = $this->decrypt($request->post("password"), $request->post("key"));
        $service = Service::with(["client", "account"])->where("service_id", $request->post('service_id'))->where("username", "=", $request->post("username"))->first();

        if (!$service) return false;
        if (!Hash::check($password, $service->password)) return false;

        return $service;
    }

    private function decrypt($encrypted, $key)
    {
        $decrypted = openssl_decrypt(hex2bin($encrypted), 'AES-256-CTR', $key, OPENSSL_RAW_DATA, config('wallet.ewallet_encryption_salt'));
        return utf8_encode(trim($decrypted));
    }

    private function send_sms($to, $message)
    {
        if (config('app.env') == "production") {
            $outbox = Outbox::where("message", "=", $message)->where("to", "=", $to)->first();
            if (!$outbox) {
                $data = [
                    "identifier" => generate_identifier(),
                    "message" => $message,
                    "to" => $to,
                    "sent" => "0",
                    "cost" => get_option('actual-sms-cost')
                ];

                $outbox = Outbox::query()->create($data);
                ProcessSMS::dispatch($to, $message, $outbox->id)->onQueue("outbox");
            }
        } else {
            Log::warning($to . " | " . $message);
        }
    }

    private function generateRandomString($strl_type = "", $length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($strl_type == "pin") $characters = '0123456789';
        if ($strl_type == "password") $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@%^&()';
        $randomString = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function daraja_account_balance($account)
    {
        $mpesaShortCode = $account->account_number;
        $consumerKey = $account->consumer_key;
        $consumerSecret = $account->consumer_secret;
        $apiUsername = $account->api_username;
        $apiPassword = $account->api_password;
        $mpesa = new Mpesa($mpesaShortCode, $consumerKey, $consumerSecret, $apiUsername, $apiPassword);
        return $mpesa->getBalance(route('balance_result_url', $account->identifier), route('balance_timeout_url'));
    }

    private function daraja_transaction_status($transaction)
    {
        $account = $transaction->account;
        $mpesaShortCode = $account->account_number;
        $consumerKey = $account->consumer_key;
        $consumerSecret = $account->consumer_secret;
        $apiUsername = $account->api_username;
        $apiPassword = $account->api_password;
        $mpesa = new Mpesa($mpesaShortCode, $consumerKey, $consumerSecret, $apiUsername, $apiPassword);
        return $mpesa->getTransactionStatus($transaction->receipt_number, "shortcode", $transaction->description, route('trx_status_result_url'), route('trx_status_timeout_url'), $transaction->payload?->original_conversation_id);
    }
}
