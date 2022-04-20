<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;

class ProductGalleryController extends Controller
{
    public function createPhoto(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            'product_id' => 'required|exists:products,id,deleted_at,NULL',
            'photo_url' => 'required|image'
        ]);

        if($validator->fails()) {
            return ResponseFormatter::error(
                null,
                $validator->errors(),
                400
            );
        }
        
        $data['photo_url'] = $request->file('photo_url')->store('assets/product', 'public');

        ProductGallery::create($data);
        
        return ResponseFormatter::success(
            $data,
            'Data foto berhasil disimpan'
        );
    }

    public function deletePhoto($id) {
        $item = ProductGallery::findorFail($id);

        if($item->delete()) {
            return ResponseFormatter::success(
                $item,
                'Data foto berhasil dihapus'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data foto gagal dihapus',
                400,
            );
        }
    }
}
