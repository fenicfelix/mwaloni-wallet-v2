<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_charges', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier');
            $table->foreignId('payment_channel_id')->nullable()->references("id")->on("payment_channels")->onDelete("set null");
            $table->double("minimum")->default(0);
            $table->double("maximum")->default(0);
            $table->double("charge")->default(0);
            $table->timestamp('added_on')->nullable()->useCurrent();
            $table->foreignId('added_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->timestamp('updated_on')->nullable()->useCurrentOnUpdate();
            $table->foreignId('updated_by')->nullable()->references("id")->on("users")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_charges');
    }
}
