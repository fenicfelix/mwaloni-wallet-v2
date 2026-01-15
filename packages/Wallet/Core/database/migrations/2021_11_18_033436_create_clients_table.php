<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier');
            $table->string('name', 50)->unique();
            $table->string('client_id', 10)->unique()->nullable();
            $table->foreignId('account_manager')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->double("balance")->default(0);
            $table->boolean("active")->default(true);
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
        Schema::dropIfExists('clients');
    }
}
