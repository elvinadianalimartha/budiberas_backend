<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftStockDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_stock_destinations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shift_source_id');

            $table->integer('quantity');

            $table->timestamps();
        });

        Schema::table('shift_stock_destinations', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')->on('products');
            
            $table->foreign('shift_source_id')
                  ->references('id')->on('shift_stocks')
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
        Schema::dropIfExists('shift_stock_destinations');
    }
}
