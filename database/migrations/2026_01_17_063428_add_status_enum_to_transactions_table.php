<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wallet\Core\Http\Enums\TransactionStatus;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status', TransactionStatus::values())->default(TransactionStatus::PENDING->value)->after('key_block');
            $table->index(['status', 'transaction_date']);
        });

        // for all transactions, update status based on their status if
        // the status is not in the enum values, set it to pending
        DB::table('transactions')->where('status_id', 1)->update(['status' => TransactionStatus::SUBMITTED->value]);
        DB::table('transactions')->where('status_id', 2)->update(['status' => TransactionStatus::SUCCESS->value]);
        DB::table('transactions')->where('status_id', 3)->update(['status' => TransactionStatus::FAILED->value]);
        DB::table('transactions')->where('status_id', 4)->update(['status' => TransactionStatus::CANCELLED->value]);
        DB::table('transactions')->where('status_id', 5)->update(['status' => TransactionStatus::REVERSING->value]);
        DB::table('transactions')->where('status_id', 6)->update(['status' => TransactionStatus::PENDING->value]);
        DB::table('transactions')->where('status_id', 7)->update(['status' => TransactionStatus::REVERSED->value]);
        DB::table('transactions')->where('status_id', 8)->update(['status' => TransactionStatus::QUERYING_STATUS->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
