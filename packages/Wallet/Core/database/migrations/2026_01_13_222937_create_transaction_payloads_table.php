<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionPayloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_payloads', function (Blueprint $table) {
            $table->id();

            $table->string("conversation_id", 255)->nullable();
            $table->string("original_conversation_id", 255)->nullable();
            
            // API Raw Data
            $table->text('raw_request')->nullable();
            $table->text('trx_payload')->nullable();
            $table->text('raw_callback')->nullable();

            $table->foreignId('transaction_id')->nullable()->references("id")->on("transactions")->onDelete("set null");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_payloads');
    }
}
