<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftstocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_stocks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');

            $table->date('shifting_date');
            $table->time('shifting_time');
            $table->integer('quantity');

            $table->timestamps();
        });

        Schema::table('shift_stocks', function (Blueprint $table) {
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
        Schema::dropIfExists('shift_stocks');
    }
}
