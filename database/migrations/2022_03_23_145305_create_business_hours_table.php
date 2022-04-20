<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shop_id');

            $table->string('day');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();

            $table->timestamps();
        });

        Schema::table('business_hours', function (Blueprint $table) {
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
        Schema::dropIfExists('business_hours');
    }
}
