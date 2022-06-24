<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('product_id');

            $table->integer('quantity');
            $table->double('subtotal');
            $table->string('order_notes')->nullable();

            $table->timestamps();
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->foreign('transaction_id')
                  ->references('id')->on('transactions')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_details');
    }
}
