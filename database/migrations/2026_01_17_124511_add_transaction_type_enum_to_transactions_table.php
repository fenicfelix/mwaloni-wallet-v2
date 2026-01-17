<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wallet\Core\Http\Enums\TransactionType;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transaction_type', TransactionType::values())->default(TransactionType::PAYMENTS->value)->after('service_id');
        });

        DB::table('transactions')->where('type_id', 1)->update(['transaction_type' => TransactionType::PAYMENTS->value]);
        DB::table('transactions')->where('type_id', 2)->update(['transaction_type' => TransactionType::ACCOUNT_BALANCE->value]);
        DB::table('transactions')->where('type_id', 3)->update(['transaction_type' => TransactionType::REVERSAL->value]);
        DB::table('transactions')->where('type_id', 4)->update(['transaction_type' => TransactionType::CASHOUT->value]);
        DB::table('transactions')->where('type_id', 5)->update(['transaction_type' => TransactionType::SERVICE_CHARGE->value]);
        DB::table('transactions')->where('type_id', 6)->update(['transaction_type' => TransactionType::DISTRIBUTE->value]);
        DB::table('transactions')->where('type_id', 7)->update(['transaction_type' => TransactionType::WITHDRAW->value]);
        DB::table('transactions')->where('type_id', 8)->update(['transaction_type' => TransactionType::REVENUE_TRANSFER->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
};
