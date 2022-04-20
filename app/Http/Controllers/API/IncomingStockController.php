<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\IncomingStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class IncomingStockController extends Controller
{
    public function createIncomingStock(Request $request) {
        $incomeStock = $request->all();

        $todayDate = Carbon::now()->toDateString();
        $todayTime = Carbon::now()->format('H:i');

        $validate = Validator::make($incomeStock, [
            'product_id' => 'required|exists:products,id,deleted_at,NULL',
            'quantity' => 'required|numeric',
            'incoming_status' => 'required|in:Tambah stok,Retur dari pembeli'
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        //Show product stock before adding incoming stock
        $stockBefore = Product::where('id', '=', $incomeStock['product_id'])->value('stock');

        $incomeStock['incoming_date'] = $todayDate;
        $incomeStock['incoming_time'] = $todayTime;

        //Store incoming stock to database
        $storeIncomeStock = IncomingStock::create($incomeStock);

        //Update product stock
        $this -> addProductStock($storeIncomeStock['product_id'], $storeIncomeStock['quantity']);

        //Show product stock after added incoming stock
        $stockAfter = Product::where('id', '=', $storeIncomeStock['product_id'])->value('stock');

        return response([
            'message' => 'Data stok masuk berhasil disimpan',
            'data' => $storeIncomeStock,
            'stockBefore' => $stockBefore,
            'stockProductNow' => $stockAfter,
        ]);
    }

    private function addProductStock($product_id, $quantity) {
        $stockBefore = Product::where('id', '=', $product_id)->value('stock');
        $addedStock = $stockBefore + $quantity;

        Product::where('id', '=', $product_id)
                ->update([
                    'stock' => $addedStock
                ]);
    }

    public function getAddedIncomingStock(Request $request) {
        $date = $request->input('date');

        $addedIncomingStocks = IncomingStock::with('product:id,product_name,deleted_at')->where('incoming_status', '=', 'Tambah stok');

        if($date) {
            $addedIncomingStocks = $addedIncomingStocks->where('incoming_date', '=', $date); 
        }

        //ubah tampilan tanggal jadi format tgl bln thn lengkap
        foreach($addedIncomingStocks as $incomingStock) {
            $incomingStock->incoming_date = Carbon::parse($incomingStock->incoming_date)->isoFormat('LL');
        }

        if($addedIncomingStocks->count() > 0) {
            return ResponseFormatter::success(
                $addedIncomingStocks->get(),
                'Data stok masuk yang ditambahkan pemilik berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data stok masuk yang ditambahkan pemilik belum ada',
                404,
            );
        }                                       
    }

    public function getReturnIncomingStock(Request $request) {
        $date = $request->input('date');

        $returnIncomingStocks = IncomingStock::with('product:id,product_name,deleted_at')->where('incoming_status', '=', 'Retur dari pembeli');
        
        if($date) {
            $returnIncomingStocks = $returnIncomingStocks->where('incoming_date', '=', $date); 
        }

        foreach($returnIncomingStocks as $incomingStock) {
            $incomingStock->incoming_date = Carbon::parse($incomingStock->incoming_date)->isoFormat('LL');
        }

        if($returnIncomingStocks->count() > 0) {
            return ResponseFormatter::success(
                $returnIncomingStocks->get(),
                'Data stok masuk dari retur pembeli berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data stok masuk dari retur pembeli belum ada',
                404,
            );
        }                                       
    }

    public function deleteIncomingStock($id) {
        $item = IncomingStock::findorFail($id);

        $itemProductID = $item->product_id;
        $itemQuantity = $item->quantity;

        $stockBefore = Product::where('id', '=', $itemProductID)->value('stock');

        if($item->delete()) {
            $this->reduceProductStock($itemProductID, $itemQuantity);

            $stockAfter = Product::where('id', '=', $itemProductID)->value('stock');

            return response([
                'data' => $item,
                'message' => 'Data stok masuk berhasil dihapus',
                'stockBefore' => $stockBefore,
                'stockProductNow' => $stockAfter,
            ]);
        } else {
            return ResponseFormatter::error(
                null,
                'Data stok masuk gagal dihapus',
                400,
            );
        }
    }

    private function reduceProductStock($product_id, $quantity) {
        $stockBefore = Product::where('id', '=', $product_id)->value('stock');

        $countReduceStock = $stockBefore - $quantity; 

        Product::where('id', '=', $product_id)
                ->update([
                    'stock' => $countReduceStock
                ]);
    }

    public function updateIncomingStock(Request $request, $id) {
        $incomeStock = IncomingStock::findorFail($id);
        $product_id = IncomingStock::where('id', '=', $id)->value('product_id');

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
        $quantityDiff = $incomeStock->quantity - $updateData['quantity'];
        $stockAfter = $stockBefore - $quantityDiff;

        $incomeStock->quantity = $updateData['quantity'];

        if($incomeStock->save()) {
            Product::where('id', '=', $product_id)
                ->update([
                    'stock' => $stockAfter
                ]);

            return ResponseFormatter::success(
                $incomeStock,
                'Jumlah stok masuk berhasil diedit'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Jumlah stok masuk gagal diedit',
                400,
            );
        }
    }
}
