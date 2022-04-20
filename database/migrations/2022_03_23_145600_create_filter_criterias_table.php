<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilterCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filter_criterias', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id');

            $table->string('criteria_header');
            $table->string('criteria_name');
            $table->string('criteria_notes');

            $table->timestamps();
        });

        Schema::table('filter_criterias', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')->on('product_categories')
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
        Schema::dropIfExists('filter_criterias');
    }
}
