<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ShippingRate;

class ShippingRatesController extends Controller
{
    public function getShippingRates() {
        $shippingRates = ShippingRate::all();

        return ResponseFormatter::success(
            $shippingRates,
            'Daftar biaya pengiriman berhasil diambil'
        );
    }
}
