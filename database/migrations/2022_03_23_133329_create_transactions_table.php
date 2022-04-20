<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_detail_id')->nullable();
            $table->unsignedBigInteger('shipping_rate_id')->nullable();

            $table->string('invoice_code');
            $table->string('shipping_type')->default('Diantar');
            $table->double('total_price');
            $table->string('transaction_status');
            $table->string('payment_method');
            $table->string('pickup_code')->nullable();
            
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('user_detail_id')
                  ->references('id')->on('user_details');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('shipping_rate_id')
                  ->references('id')->on('shipping_rates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
