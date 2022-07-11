<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ShopInfo;

class ShopInfoController extends Controller
{
    public function getShopInfoForCustomer() {
       $shopInfo = ShopInfo::select(
            'shop_address', 
            'address_notes', 
            'latitude',
            'longitude',
            'phone_number',
            'open_status'
        )->get();

        return ResponseFormatter::success(
            $shopInfo,
            'Data toko untuk pelanggan berhasil diambil'
        );
    }
}
