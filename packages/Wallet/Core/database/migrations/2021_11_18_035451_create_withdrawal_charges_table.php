<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_charges', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier');
            $table->double("minimum")->default(0);
            $table->double("maximum")->default(0);
            $table->double("charge")->default(0);
            $table->foreignId('added_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('updated_by')->nullable()->references("id")->on("users")->onDelete("set null");
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
        Schema::dropIfExists('withdrawal_charges');
    }
}
