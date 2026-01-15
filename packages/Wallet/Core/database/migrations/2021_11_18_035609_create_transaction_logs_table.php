<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier');
            $table->foreignId('type_id')->nullable()->references("id")->on("transaction_types")->onDelete("set null");
            $table->foreignId('account_id')->nullable()->references("id")->on("accounts")->onDelete("set null");
            $table->foreignId('client_id')->nullable()->references("id")->on("clients")->onDelete("set null");
            $table->foreignId('service_id')->nullable()->references("id")->on("services")->onDelete("set null");
            $table->double("amount")->default(0);
            $table->string("reference", 255)->nullable();
            $table->string("status", 10)->nullable();
            $table->string("status_description")->nullable();
            $table->foreignId('initiated_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->timestamp('initiated_on')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_logs');
    }
}
