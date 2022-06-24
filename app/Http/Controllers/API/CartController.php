<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    //get 
    public function getCart(){
        $carts = Cart::with('product.productGalleries','product.productCategory')->where('user_id', '=', Auth::user()->id);
        
        if($carts->count() > 0) {
            return ResponseFormatter::success(
                $carts->orderBy('created_at', 'DESC')->get(), 
                'Data keranjang berhasil ditampilkan'
            );
        } else {
            return ResponseFormatter::error(
                null, 
                'Data keranjang belum ada',
                404,
            );
        }
    }

    //create
    public function addToCart(Request $request) {
        //ambil data user & produk yang mau dimasukkan ke keranjang 
        $data = $request->all();
        $validate = Validator::make($data, [
           'product_id' => 'required|exists:products,id,deleted_at,NULL',
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        //cek kalau status produk nonaktif, maka tdk bisa dimasukkan ke cart
        $product = Product::where('id', '=', $data['product_id']);
        if($product->value('stock_status') == 'Tidak aktif') {
            return ResponseFormatter::error(
                null,
                'Stok produk habis atau tidak aktif',
                405
            );
        } else {
            //cek apakah sudah pernah dimasukkan ke keranjang oleh user tsb
            $checkProductInCart = Cart::where('product_id', '=', $data['product_id'])
                                    ->where('user_id', '=', Auth::user()->id)
                                    ->value('id');

            //jika sudah pernah (ditandai adanya id cart)
            if($checkProductInCart != null) {
                $cartData = Cart::find($checkProductInCart);

                //jika qty yg ada di cart sdh sama dgn stok produk, maka tidak bisa ditambahkan lagi
                if($cartData->quantity == $product->value('stock')) {
                    return ResponseFormatter::error(
                        null,
                        'Jumlah produk sudah mencapai batas maksimal',
                        405,
                    );
                } else {
                    $cartData->quantity = $cartData->quantity + 1;
                    if($cartData->save()){
                        return ResponseFormatter::success(
                            $cartData,
                            'Produk berhasil ditambahkan ke keranjang'
                        );
                    }
                }
            //jika blm pernah, buat data cart baru dgn qty = 1
            } else {
                $data['user_id'] = Auth::user()->id;
                $data['quantity'] = 1;
                $storedCart = Cart::create($data); 
                return ResponseFormatter::success(
                    $storedCart,
                    'Produk berhasil ditambahkan ke keranjang'
                );
            }
        }
    }
    
    //update qty in cart
    public function updateQty($id, Request $request) {
        $cartToUpdate = Cart::findOrFail($id);
        $newQty = $request->all();

        //max qty product in cart = stock product
        $productId = $cartToUpdate->product_id;
        $maxQty = Product::where('id', '=', $productId)->value('stock');

        $validate = Validator::make($newQty, [
            'quantity' => "required|numeric|max:$maxQty"
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        $cartToUpdate->quantity = $newQty['quantity'];

        if($cartToUpdate->save()) {
            return ResponseFormatter::success(
                $cartToUpdate,
                'Jumlah produk dalam keranjang berhasil diperbarui'
            );
        }
    }

    //update order notes in cart
    public function updateOrderNotes($id, Request $request) {
        $cartToUpdate = Cart::findOrFail($id);
        $notes = $request->input('order_notes');
       
        $cartToUpdate->order_notes = $notes;
        if($cartToUpdate->save()) {
            return ResponseFormatter::success(
                $cartToUpdate,
                'Catatan berhasil tersimpan'
            );
        }
    }

    //delete
    public function deleteCart($id) {
        $item = Cart::findOrFail($id);

        if($item->delete()) {
            return ResponseFormatter::success(
                $item,
                'Data keranjang berhasil dihapus'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data keranjang gagal dihapus',
                400,
            );
        }
    }

    //update isSelected val
    public function updateIsSelected($id, Request $request) {
        $cartToUpdate = Cart::findOrFail($id);
        $value = $request->input('is_selected');

        $cartToUpdate->is_selected = $value;
        if($cartToUpdate->save()) {
            return ResponseFormatter::success(
                $cartToUpdate,
                "Cart is selected = $value"
            );
        }
    }

    public function updateIsSelectedAllCart(Request $request) {
        $value = $request->input('is_selected');
        $cartToUpdate = Cart::with(['product' => function($query) {
                            $query->where('product.stock_status', '=', 'Aktif');
                        }])
                        ->where('user_id', '=', Auth::user()->id)
                        ->update([
                            'is_selected' => $value
                        ]);
        return ResponseFormatter::success(
            $cartToUpdate,
            "All cart is selected = $value"
        );
    }
}
