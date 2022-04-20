<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_stocks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');

            $table->date('out_date');
            $table->time('out_time');
            $table->integer('quantity');
            $table->string('out_status'); //Retur ke supplier/terjual offline/pengalihan stok ke eceran

            $table->timestamps();
        });

        Schema::table('out_stocks', function (Blueprint $table) {
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
        Schema::dropIfExists('out_stocks');
    }
}
