<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressAttributesToShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_info', function (Blueprint $table) {
            $table->string('shop_regency')->after('fcm_token');
            $table->string('shop_district')->after('shop_regency');
            $table->string('address_notes')->after('shop_address')->nullable();
            $table->double('latitude')->after('address_notes');
            $table->double('longitude')->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_info', function (Blueprint $table) {
            $table->dropColumn('shop_regency');
            $table->dropColumn('shop_district');
            $table->dropColumn('address_notes');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
