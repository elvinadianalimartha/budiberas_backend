<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Models\IncomingStock;
use App\Models\Product;
use App\Models\OutStock;
use App\Traits\UpdateStockTrait;

class OutStockController extends Controller
{
    use UpdateStockTrait;

    public function createOutStock(Request $request) {
        $outStock = $request->all();

        $todayDate = Carbon::now()->toDateString();
        $todayTime = Carbon::now()->format('H:i:s');

        //Show product stock before adding out stock
        $stockBefore = Product::where('id', '=', $outStock['product_id'])->value('stock');

        $validate = Validator::make($outStock, [
            'product_id' => 'required|exists:products,id,deleted_at,NULL',
            'quantity' => "required|numeric|max:$stockBefore",
            'out_status' => 'required|in:Retur ke supplier,Penjualan offline,Penjualan online'
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        $outStock['out_date'] = $todayDate;
        $outStock['out_time'] = $todayTime;

        //Store out stock to database
        $storeOutStock = OutStock::create($outStock);

        //Update product stock
        $this->reduceProductStock($storeOutStock['product_id'], $storeOutStock['quantity']);

        //Show product stock after out stock
        $stockAfter = Product::where('id', '=', $storeOutStock['product_id'])->value('stock');

        return response([
            'message' => 'Data stok keluar berhasil disimpan',
            'data' => $storeOutStock,
            'stockBefore' => $stockBefore,
            'stockProductNow' => $stockAfter,
        ]);
    }

    public function getOutStock(Request $request) {
        $date = $request->input('date');

        $outStocks = OutStock::with('product:id,product_name,stock,deleted_at')->where('out_status', '=', 'Retur ke supplier');

        if($date) {
            $outStocks = $outStocks->where('out_date', '=', $date); 
        }

        if($outStocks->count() > 0) {
            return ResponseFormatter::success(
                $outStocks->orderBy('created_at', 'DESC')->get(),
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

    public function countMaxOutQty($id) {
        $outStock = OutStock::findorFail($id);
        $product_id = $outStock->product_id;

        $totalIncomingStock = IncomingStock::where('product_id', '=', $product_id)->sum('quantity');

        $totalOutStock = OutStock::where('id', '!=', $id)
                                ->where('product_id', '=', $product_id)
                                ->sum('quantity');

        $maxOutQty = $totalIncomingStock - $totalOutStock;
        return $maxOutQty;
    }

    public function updateOutStock(Request $request, $id) {
        $outStock = OutStock::findorFail($id);

        $product_id = $outStock->product_id;
        $out_qty = $outStock->quantity;

        $updateData = $request->all();

        $product = Product::where('id', '=', $product_id);
        $stockBefore = $product->value('stock');

        $countMaxQty = $this->countMaxOutQty($id);

        if($stockBefore == 0) {
            $maxOutQty = $out_qty;
        } else {
            $maxOutQty = $countMaxQty;
        }

        $validate = Validator::make($updateData, [
            'quantity' => "numeric|max:$maxOutQty",
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        //Count current product stock
        $quantityDiff = $updateData['quantity'] - $outStock->quantity;
        $stockAfter = $stockBefore - $quantityDiff;

        $outStock->quantity = $updateData['quantity'];

        if($outStock->save()) {
            $product->update([
                'stock' => $stockAfter
            ]);
            
            if($product->value('stock') < 1) {
                $product->update([
                    'stock_status' => 'Tidak aktif'
                ]);
            } else {
                if($product->value('stock_notes') != 'Nonactivate by owner') {
                    $product->update([
                        'stock_status' => 'Aktif'
                    ]);
                }
            }
            
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
