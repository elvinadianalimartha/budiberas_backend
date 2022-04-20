<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shop_id');

            $table->double('max_distance')->nullable();
            $table->double('min_order_price')->nullable();
            $table->double('shipping_price')->default(0);
            $table->string('shipping_name')->default('Standar');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->foreign('shop_id')
                  ->references('id')->on('shop_info')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_rates');
    }
}
