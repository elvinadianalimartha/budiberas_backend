<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Models\Product;
use App\Models\OutStock;

class OutStockController extends Controller
{
    public function createOutStock(Request $request) {
        $outStock = $request->all();

        $todayDate = Carbon::now()->toDateString();
        $todayTime = Carbon::now()->format('H:i');

        $validate = Validator::make($outStock, [
            'product_id' => 'required|exists:products,id,deleted_at,NULL',
            'quantity' => 'required|numeric',
            'out_status' => 'required|in:Retur ke supplier,Penjualan offline,Penjualan online'
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        //Show product stock before adding out stock
        $stockBefore = Product::where('id', '=', $outStock['product_id'])->value('stock');

        $outStock['out_date'] = $todayDate;
        $outStock['out_time'] = $todayTime;

        //Store out stock to database
        $storeOutStock = OutStock::create($outStock);

        //Update product stock
        $this -> reduceProductStock($storeOutStock['product_id'], $storeOutStock['quantity']);

        //Show product stock after out stock
        $stockAfter = Product::where('id', '=', $storeOutStock['product_id'])->value('stock');

        return response([
            'message' => 'Data stok keluar berhasil disimpan',
            'data' => $storeOutStock,
            'stockBefore' => $stockBefore,
            'stockProductNow' => $stockAfter,
        ]);
    }

    private function reduceProductStock($product_id, $quantity) {
        $stockBefore = Product::where('id', '=', $product_id)->value('stock');

        $countReduceStock = $stockBefore - $quantity; 

        Product::where('id', '=', $product_id)
                ->update([
                    'stock' => $countReduceStock
                ]);
    }

    public function getOutStock(Request $request) {
        $date = $request->input('date');

        $outStocks = OutStock::with('product:id,product_name,deleted_at')->where('out_status', '=', 'Retur ke supplier');

        if($date) {
            $outStocks = $outStocks->where('out_date', '=', $date); 
        }

        //ubah tampilan tanggal jadi format tgl bln thn lengkap
        foreach($outStocks as $outStock) {
            $outStock->out_date = Carbon::parse($outStock->out_date)->isoFormat('LL');
        }

        if($outStocks->count() > 0) {
            return ResponseFormatter::success(
                $outStocks->get(),
                'Data stok keluar dari retur ke supplier berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data stok keluar dari retur ke supplier belum ada',
                404,
            );
        }                                       
    }

    public function deleteOutStock($id) {
        $item = OutStock::findorFail($id);

        $itemProductID = $item->product_id;
        $itemQuantity = $item->quantity;

        $stockBefore = Product::where('id', '=', $itemProductID)->value('stock');

        if($item->delete()) {
            $this->addProductStock($itemProductID, $itemQuantity);

            $stockAfter = Product::where('id', '=', $itemProductID)->value('stock');

            return response([
                'data' => $item,
                'message' => 'Data stok keluar berhasil dihapus',
                'stockBefore' => $stockBefore,
                'stockProductNow' => $stockAfter,
            ]);
        } else {
            return ResponseFormatter::error(
                null,
                'Data stok keluar gagal dihapus',
                400,
            );
        }
    }

    private function addProductStock($product_id, $quantity) {
        $stockBefore = Product::where('id', '=', $product_id)->value('stock');
        $addedStock = $stockBefore + $quantity;

        Product::where('id', '=', $product_id)
                ->update([
                    'stock' => $addedStock
                ]);
    }

    public function updateOutStock(Request $request, $id) {
        $outStock = OutStock::findorFail($id);
        $product_id = OutStock::where('id', '=', $id)->value('product_id');

        $updateData = $request->all();

        $validate = Validator::make($updateData, [
            'quantity' => 'numeric',
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        //Count current product stock
        $stockBefore = Product::where('id', '=', $product_id)->value('stock');
        $quantityDiff = $updateData['quantity'] - $outStock->quantity;
        $stockAfter = $stockBefore - $quantityDiff;

        $outStock->quantity = $updateData['quantity'];

        if($outStock->save()) {
            Product::where('id', '=', $product_id)
                ->update([
                    'stock' => $stockAfter
                ]);

            return ResponseFormatter::success(
                $outStock,
                'Jumlah stok keluar berhasil diedit'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Jumlah stok keluar gagal diedit',
                400,
            );
        }
    }
}
