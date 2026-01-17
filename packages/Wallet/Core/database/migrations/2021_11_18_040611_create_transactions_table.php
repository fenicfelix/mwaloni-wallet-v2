<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier');
            $table->string("key_block", 50)->unique();
            $table->string("account_number", 30)->nullable();
            $table->string("account_name", 100)->nullable();
            $table->string("account_reference", 30)->nullable();
            $table->string("reference", 60)->nullable();
            $table->string("description", 100)->nullable();
            $table->foreignId('payment_channel_id')->nullable()->references("id")->on("payment_channels")->onDelete("set null");
            $table->foreignId('account_id')->nullable()->references("id")->on("accounts")->onDelete("set null");
            $table->foreignId('service_id')->nullable()->references("id")->on("services")->onDelete("set null");
            $table->foreignId('type_id')->nullable()->references("id")->on("transaction_types")->onDelete("set null");
            $table->string("order_number", 30)->nullable();
            $table->string('message_id', 30)->nullable();

            // Amounts
            $table->double("requested_amount")->default(0);
            $table->double("disbursed_amount")->default(0);
            $table->double("transaction_charges")->default(0);
            $table->double("system_charges")->default(0);
            $table->double("sms_charges")->default(0);
            $table->double("revenue")->default(0);

            $table->string("receipt_number", 30)->nullable();

            // Status
            $table->foreignId('status_id')->nullable()->references("id")->on("statuses")->onDelete("set null");
            $table->string("result_description")->nullable();

            $table->foreignId('requested_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('reversed_by')->nullable()->references("id")->on("users")->onDelete("set null");

            $table->timestamp('transaction_date')->nullable()->useCurrent();
            $table->timestamp('requested_on')->nullable()->useCurrent();
            $table->timestamp('reversed_on')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
