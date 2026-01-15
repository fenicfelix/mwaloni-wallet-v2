<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outboxes', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier');
            $table->string('message', 255);
            $table->string('to', 20);
            $table->boolean("sent")->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('sent_at')->nullable()->useCurrentOnUpdate();
            $table->double('cost')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outboxes');
    }
}
