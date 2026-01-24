<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier');
            $table->string("name", 100);
            $table->string('country_code', 5)->default('KE');
            $table->string('country_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('branch_code')->nullable();
            $table->decimal("working_balance", 10, 2)->default(0);
            $table->decimal("utility_balance", 10, 2)->default(0);
            $table->decimal("withheld_amount", 10, 2)->default(0);
            $table->foreignId('account_type_id')->nullable()->references("id")->on("account_types")->onDelete("set null");
            $table->foreignId('currency_id')->nullable()->references("id")->on("currencies")->onDelete("set null");
            $table->string('api_username', 30)->nullable();
            $table->string('api_password', 30)->nullable();
            $table->string('account_number', 20)->nullable();
            $table->string('cif')->nullable();
            $table->string('pesalink_cif')->nullable();
            $table->string('address')->nullable();
            $table->string('consumer_key', 100)->nullable();
            $table->string('consumer_secret', 100)->nullable();
            $table->decimal("revenue", 10, 2)->default(0);
            $table->boolean("active")->default(true);
            $table->boolean("auto_fetch_balance")->default(true);
            $table->foreignId('managed_by')->nullable()->references("id")->on("users")->onDelete("set null");
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
        Schema::dropIfExists('accounts');
    }
}
