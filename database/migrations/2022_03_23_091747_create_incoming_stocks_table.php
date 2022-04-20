<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomingStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_stocks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');

            $table->date('incoming_date');
            $table->time('incoming_time');
            $table->integer('quantity');
            $table->string('incoming_status'); //Tambah stok/retur dari pembeli/dialihkan dari grosir

            $table->timestamps();
        });

        Schema::table('incoming_stocks', function (Blueprint $table) {
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
        Schema::dropIfExists('incoming_stocks');
    }
}
