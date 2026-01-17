<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique();
            $table->string('service_id', 10)->unique()->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean("active")->default(true);
            $table->foreignId('client_id')->nullable()->references("id")->on("clients")->onDelete("set null");
            $table->double("system_charges")->default(0);
            $table->double('max_trx_amount')->default(0);
            $table->double("sms_charges")->default(0);
            $table->string("username", 20);
            $table->string("password", 100);
            $table->string('callback_url')->nullable();
            $table->double('revenue')->default(0);
            $table->foreignId('account_id')->nullable()->references("id")->on("accounts")->onDelete("set null");
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
        Schema::dropIfExists('services');
    }
}
