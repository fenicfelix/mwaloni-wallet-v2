<?php

namespace Wallet\Core\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait Jenga
{

private function jenga_fetch_token($account)
{
    $token = $this->jenga_generate_token($account);
    if(!$token) {
        info("JENGA TOKEN ERROR: " . json_encode($token));
        return false;
    }
    info('jenga_fetch_token: ' . json_encode($token));
    return $token->accessToken;
}

private function jenga_generate_token($account)
{
    $url = config('wallet.jenga.authenticate_endpoint');
    $data = [
        "merchantCode" => $account->account_number,
        "consumerSecret" => $account->consumer_secret
    ];

    info("JENGA_TOKEN_DATA: " . json_encode($data));

    $client = new Client();
    try {
        $response = $client->request('POST', $url, [
            'body' => json_encode($data),
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-Key' => $account->consumer_key,
            ],
        ]);
        info("JENGA_TOKEN_RESPONSE: " . json_encode($response->getBody()));
        $result = json_decode($response->getBody());
        if (isset($result->status) && !$result->status) {
            return false;
        } else {
            return $result;
        }
    } catch (ClientException $e) {
        info("JENGA TOKEN ERROR: " . json_encode($e->getMessage()));
    }
}

private function jenga_generate_signature($signaturePlainText)
{
    $privateKey = openssl_pkey_get_private("file://" . public_path('Cert/jenga/privatekey.pem'));
    openssl_sign($signaturePlainText, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $signature = base64_encode($signature);
    return $signature;
}

private function jenga_fetch_balance($account)
{
    info("----------------------------- JENGA QUERY BALANCE ---------------------------");
    info(config('wallet.jenga.balances_endpoint') . $account->country_code . "/" . $account->account_number);
    $signaturePlainText = $account->country_code . $account->account_number;
    $url = config('wallet.jenga.balances_endpoint') . $account->country_code . "/" . $account->account_number;
    return $this->jenga_curl_get($signaturePlainText, $account, $url);
}

private function jenga_get_mini_statement($account)
{
    info("----------------------------- GET MINI STATEMENT ---------------------------");
    $signaturePlainText = $account->country_code . $account->account_number;
    $url = config('wallet.jenga.mini_statement_endpoint') . $account->country_code . "/" . $account->account_number;
    info("BALANCE URL: " . $url);
    return $this->jenga_curl_get($signaturePlainText, $account, $url);
}

private function jenga_get_full_statement($account, $start_date, $end_date, $limit = 3)
{
    info("----------------------------- GET FULL STATEMENT ---------------------------");
    $signaturePlainText = $account->account_number . $account->country_code . date("Y-m-d");
    $url = config('wallet.jenga.full_statement_endpoint');
    $data = [
        "countryCode" => $account->country_code,
        "accountNumber" => $account->account_number,
        "fromDate" => $start_date,
        "toDate" => $end_date,
        "limit" => $limit
    ];
    info("DATA: " . json_encode($data));

    return $this->jenga_curl_post($signaturePlainText, $account, $url, $data);
}

private function jenga_send_to_equity($account, $destination)
{
    // $signaturePlainText = "source.accountNumber,transfer.amount,transfer.currencyCode,transfer.reference";
    info("----------------------------- EQUITY TRANSFER ---------------------------");
    $destination = (object) $destination;
    $signaturePlainText = $account->account_number . $destination->amount . $destination->currencyCode . $destination->reference;
    $url = config('wallet.jenga.remittance_endpoint') . "internalBankTransfer";
    $data = [
        "source" => [
            "countryCode" => $account->country_code,
            "name" => $account->name,
            "accountNumber" => $account->account_number
        ],
        "destination" => [
            "type" => "bank",
            "countryCode" => $destination->countryCode,
            "name" => $destination->accountName,
            "accountNumber" => $destination->accountNumber
        ],
        "transfer" => [
            "type" => "InternalFundsTransfer",
            "amount" => $destination->amount,
            "currencyCode" => $destination->currencyCode,
            "reference" => $destination->reference,
            "date" => $destination->date,
            "description" => $destination->description
        ]
    ];

    info("DATA: " . json_encode($data));

    return $this->jenga_curl_post($signaturePlainText, $account, $url, $data);
}

private function jenga_send_to_mobile($account, $destination)
{
    $destination = (object) $destination;
    info("----------------------------- SEND TO MPESA ---------------------------");
    // Mpesa - transfer.amount transfer.currencyCode transfer.reference source.accountNumber
    // Equity - source.accountNumber transfer.amount transfer.currencyCode transfer.reference
    if ($destination->walletName == "Mpesa") $signaturePlainText = $destination->amount . $destination->currencyCode . $destination->reference . $account->account_number;
    else $signaturePlainText = $account->account_number . $destination->amount . $destination->currencyCode . $destination->reference;
    $url = config('wallet.jenga.remittance_endpoint') . "sendMobile";
    $data = [
        "source" => [
            "countryCode" => $account->country_code,
            "name" => $account->name,
            "accountNumber" => $account->account_number
        ],
        "destination" => [
            "type" => "mobile",
            "countryCode" => $destination->countryCode,
            "name" => $destination->accountName,
            "mobileNumber" => $destination->mobileNumber,
            "walletName" => $destination->walletName,
        ],
        "transfer" => [
            "type" => "MobileWallet",
            "amount" => $destination->amount,
            "currencyCode" => $destination->currencyCode,
            "date" => $destination->date,
            "description" => $destination->description
        ]
    ];

    info("MPESA PAYLOAD: " . json_encode($data));
    info("MPESA URL: " . $url);

    return $this->jenga_curl_post($signaturePlainText, $account, $url, $data);
}

private function jenga_send_rtgs($account, $destination)
{
    //transfer.reference transfer.date source.accountNumber, destination.accountNumber transfer.amount
    info("----------------------------- RTGS ---------------------------");
    info(json_encode($account));
    info(json_encode($destination));
    $destination = (object) $destination;
    $signaturePlainText = $destination->reference . $destination->date . $account->account_number . $destination->accountNumber . $destination->amount;
    $url = config('wallet.jenga.remittance_endpoint') . "rtgs";
    $data = [
        "source" => [
            "currency" => $account->currency->code,
            "countryCode" => $account->country_code,
            "name" => $account->name,
            "accountNumber" => $account->account_number
        ],
        "destination" => [
            "type" => "bank",
            "countryCode" => $destination->countryCode,
            "name" => $destination->accountName,
            "bankCode" => $destination->bankCode,
            "accountNumber" => $destination->accountNumber
        ],
        "transfer" => [
            "type" => "RTGS",
            "amount" => $destination->amount,
            "currencyCode" => $destination->currencyCode,
            "reference" => $destination->reference,
            "date" => $destination->date,
            "description" => $destination->description
        ]
    ];
    return $this->jenga_curl_post($signaturePlainText, $account, $url, $data);
}

private function jenga_send_swift($account, $destination)
{
    //transfer.reference transfer.date source.accountNumber destination.accountNumber transfer.amount
    info("----------------------------- SWIFT ---------------------------");
    $destination = (object) $destination;
    $signaturePlainText = $destination->reference . $destination->date . $account->account_number . $destination->accountNumber . $destination->amount;
    $url = config('wallet.jenga.remittance_endpoint') . "swift";
    $data = [
        "source" => [
            "sourceCurrency" => $account->currency->code,
            "countryCode" => $account->country_code,
            "name" => $account->name,
            "accountNumber" => $account->account_number
        ],
        "destination" => [
            "type" => "bank",
            "currency" => $destination->currencyCode,
            "countryCode" => $destination->countryCode,
            "name" => $destination->accountName,
            "bankBic" => $destination->bankBic,
            "accountNumber" => $destination->accountNumber,
            "addressline1" => $destination->addressline1
        ],
        "transfer" => [
            "type" => "SWIFT",
            "amount" => $destination->amount,
            "currencyCode" => $destination->currencyCode,
            "reference" => $destination->reference,
            "date" => $destination->date,
            "description" => $destination->description,
            "chargeOption" => $destination->chargeOption
        ]
    ];
    return $this->jenga_curl_post($signaturePlainText, $account, $url, $data);
}

private function jenga_pesalink_bank($account, $destination)
{
    //transfer.amount transfer.currencyCode transfer.reference destination.name source.accountNumber
    info("----------------------------- PESALINK BANK ---------------------------");
    $destination = (object) $destination;
    $signaturePlainText = $destination->amount . $destination->currencyCode . $destination->reference . $destination->accountName . $account->account_number;
    $url = config('wallet.jenga.remittance_endpoint') . "pesalinkacc";
    $data = [
        "source" => [
            "countryCode" => $account->country_code,
            "name" => $account->name,
            "accountNumber" => $account->account_number
        ],
        "destination" => [
            "type" => "bank",
            "countryCode" => $destination->countryCode,
            "name" => $destination->accountName,
            "bankCode" => $destination->bankCode,
            "accountNumber" => $destination->accountNumber
        ],
        "transfer" => [
            "type" => "PesaLink",
            "amount" => $destination->amount,
            "currencyCode" => $destination->currencyCode,
            "reference" => $destination->reference,
            "date" => $destination->date,
            "description" => $destination->description,
        ]
    ];

    return $this->jenga_curl_post($signaturePlainText, $account, $url, $data);
}

private function jenga_pesalink_mobile($account, $destination)
{
    //transfer.amount transfer.currencyCode transfer.reference destination.name source.accountNumber
    info("----------------------------- PESALINK MOBILE ---------------------------");
    $destination = (object) $destination;
    $signaturePlainText = $destination->amount . $destination->currencyCode . $destination->reference . $destination->accountName . $account->account_number;
    $url = config('wallet.jenga.remittance_endpoint') . "pesalinkMobile";
    info($url);
    $data = [
        "source" => [
            "countryCode" => $account->country_code,
            "name" => $account->name,
            "accountNumber" => $account->account_number
        ],
        "destination" => [
            "type" => "mobile",
            "countryCode" => $destination->countryCode,
            "name" => $destination->accountName,
            "bankCode" => $destination->bankCode,
            "mobileNumber" => $destination->accountNumber,
        ],
        "transfer" => [
            "type" => "PesaLink",
            "amount" => $destination->amount,
            "currencyCode" => $destination->currencyCode,
            "reference" => $destination->reference,
            "date" => date('Y-m-d'),
        ]
    ];

    return $this->jenga_curl_post($signaturePlainText, $account, $url, $data);
}

private function jenga_curl_get($signaturePlainText, $account, $url)
{
    $client = new Client();
    try {
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->jenga_fetch_token($account),
                'signature' => $this->jenga_generate_signature($signaturePlainText),
            ],
        ]);

        return $response->getBody();
    } catch (ClientException $e) {
        return $e->getResponse()->getBody();
    }
}

private function jenga_curl_post($signaturePlainText, $account, $url, $data)
{
    $client = new Client();

    try {
        $response = $client->request('POST', $url, [
            'body' => json_encode($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->jenga_fetch_token($account),
                'Content-Type' => 'application/json',
                'signature' => $this->jenga_generate_signature($signaturePlainText),
            ],
        ]);

        return $response->getBody();
    } catch (ClientException $e) {
        return $e->getResponse()->getBody();
    }
}

}