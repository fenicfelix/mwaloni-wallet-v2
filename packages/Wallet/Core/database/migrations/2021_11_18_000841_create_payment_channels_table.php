<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->string("name", 50)->unique();
            $table->string("slug", 50)->unique();
            $table->string("description")->nullable();
            $table->boolean("active")->default(true);
            $table->foreignId('account_type_id')->nullable()->references("id")->on("account_types")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_channels');
    }
}
