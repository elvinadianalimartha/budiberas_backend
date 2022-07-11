<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('shop_info')->insert([
            'email' => 'elvinadianalimartha@gmail.com',
            'password' => Hash::make('Anya<3peanut'),
            'shop_regency' => 'KOTA YOGYAKARTA',
            'shop_district' => 'MANTRIJERON',
            'shop_address' => 'Jalan Parangtritis no. 76',
            'address_notes' => 'Seberang Pasar Prawirotaman',
            'latitude' => -7.819700,
            'longitude' => 110.367940,
            'phone_number' => '0895425380201',
            'open_status' => 'Buka',
        ]);

        DB::table('shipping_rates')->insert([
            'shop_id' => 1,
            'shipping_price' => 2000,
        ]);
    }
}
