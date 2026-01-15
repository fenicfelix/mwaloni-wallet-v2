<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('total_transactions')->default(0);
            $table->unsignedBigInteger('successful_transactions')->default(0);
            $table->unsignedBigInteger('pending_transactions')->default(0);
            $table->unsignedBigInteger('failed_transactions')->default(0);
            $table->decimal('total_spent', 18, 2)->default(0);
            $table->decimal('total_revenue', 18, 2)->default(0);
            $table->decimal('available_revenue', 18, 2)->default(0);
            $table->decimal('total_sms_cost', 18, 2)->default(0);
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
        Schema::dropIfExists('transaction_metrics');
    }
}
