<?php

namespace Wallet\Core\Services;

class PayloadGeneratorService {

    public function generatePayload($channel, $data, $amount): ?array
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
}