<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\PaymentChannel;

class PaymentChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentChannels = [
            [
                "name" => "Daraja B2C",
                "slug" => "daraja-mobile",
                "description" => "Daraja Account to Mobile Wallet",
                "account_type_id" => 1
            ],
            [
                "name" => "Daraja Paybill",
                "slug" => "daraja-paybill",
                "description" => "Daraja Working Account to Daraja Utility Account",
                "account_type_id" => 1
            ],
            [
                "name" => "Daraja Buy Goods",
                "slug" => "daraja-till",
                "description" => "Daraja Working Account to Daraja Merchant Account",
                "account_type_id" => 1
            ],
            [
                "name" => "Jenga Pesalink",
                "slug" => "jenga-pesalink-bank",
                "description" => "Pesalink to Bank",
                "account_type_id" => 2
            ],
            [
                "name" => "Jenga to Mobile",
                "slug" => "jenga-pesalink-mobile",
                "description" => "Pesalink to Mobile Wallet",
                "account_type_id" => 2
            ],
            [
                "name" => "Daraja Transfer",
                "slug" => "daraja-transfer",
                "description" => "Daraja Utility Account to Daraja Working Account",
                "account_type_id" => 1
            ],
            [
                "name" => "Jenga IFT",
                "slug" => "jenga-ift",
                "description" => "Equity to Equity payment",
                "account_type_id" => 2
            ],
            [
                "name" => "NCBA IFT",
                "slug" => "ncba-ift",
                "description" => "NCBA to NCBA via IFT",
                "account_type_id" => 3
            ],
            [
                "name" => "NCBA EFT",
                "slug" => "ncba-eft",
                "description" => "NCBA to Other Bank via EFT",
                "account_type_id" => 3
            ],
            [
                "name" => "NCBA RTGS",
                "slug" => "ncba-rtgs",
                "description" => "NCBA to Other Bank via RTGS",
                "account_type_id" => 3
            ],
            [
                "name" => "NCBA Pesalink",
                "slug" => "ncba-pesalink",
                "description" => "NCBA to Other Bank via Pesalink",
                "account_type_id" => 3
            ],
            [
                "name" => "NCBA Mobile",
                "slug" => "ncba-mobile",
                "description" => "NCBA to Mobile",
                "account_type_id" => 3
            ],
            [
                "name" => "NCBA KPLC Postpaid",
                "slug" => "ncba-kplc-postpaid",
                "description" => "NCBA KPLC Postpaid",
                "account_type_id" => 3
            ]
        ];

        foreach ($paymentChannels as $paymentChannel) {
            if (!PaymentChannel::where("slug", $paymentChannel["slug"])->exists()) {
                PaymentChannel::query()->create([
                    "identifier" => generate_identifier(),
                    "name" => $paymentChannel["name"],
                    "slug" => $paymentChannel["slug"],
                    "description" => $paymentChannel["description"],
                    "account_type_id" => $paymentChannel["account_type_id"],
                    "active" => "1"
                ]);
            } else {
                PaymentChannel::where("slug", $paymentChannel["slug"])->update(['description' => $paymentChannel["description"], "name" => $paymentChannel["name"]]);
            }
        }
    }
}
