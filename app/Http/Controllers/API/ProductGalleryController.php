<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\File;

class ProductGalleryController extends Controller
{
    public function getPhoto($productId){
        $photo = ProductGallery::where('product_id', '=', $productId);
        if($photo->count() > 0) {
            return ResponseFormatter::success(
                $photo->get(),
                'Data foto berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data foto gagal diambil',
                400,
            );
        }
    }

    public function addPhoto(Request $request, $productId) {
        $data = $request->all();

        $validator = Validator::make($data, [
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

        //$url = 'http://budiberas-backend.test/storage/'.$data['photo_url'];

        ProductGallery::create([
            'product_id' => $productId,
            'photo_url' => $data['photo_url'],
        ]);
        
        return ResponseFormatter::success(
            $data,
            'Data foto berhasil disimpan'
        );
    }

    public function deletePhoto($id) {
        $item = ProductGallery::findorFail($id);

        if($item->forceDelete()) {
            
            File::delete(public_path().'/storage/'.$item->photo_url);
            
            return response([
                'data' => $item,
                'message' => 'Data foto berhasil dihapus'
            ]);
        } else {
            return ResponseFormatter::error(
                null,
                'Data foto gagal dihapus',
                400,
            );
        }
    }

    public function updatePhoto(Request $request, $id) {
        $item = ProductGallery::findorFail($id);

        $data = $request->all();

        $validator = Validator::make($data, [
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
        
        //Save past url to be deleted
        $urlBeforeUpdated = $item->photo_url;

        //Updating to new url
        $item->photo_url = $data['photo_url'];

        if($item->save()) {
            File::delete(public_path().'/storage/'.$urlBeforeUpdated);
            return ResponseFormatter::success(
                $item,
                'Foto produk berhasil diperbarui'
            );
        } else {
            return response([
                'message' => 'Foto produk gagal diperbarui',
                'data' => null,
            ],400);
        }
    }
}
