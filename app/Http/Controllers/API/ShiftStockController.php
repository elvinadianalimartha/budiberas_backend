<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Models\Product;
use App\Models\ShiftStock;
use App\Models\ShiftStockDestination;
use App\Traits\UpdateStockTrait;

class ShiftStockController extends Controller
{
    use UpdateStockTrait;

    public function getShiftStock() {
        $shiftStockData = ShiftStock::with([
            'product:id,product_name,size,deleted_at', 
            'shiftStockDestination.product:id,product_name,deleted_at'
        ]);

        if($shiftStockData->count() > 0) {
            return ResponseFormatter::success(
                $shiftStockData->orderBy('created_at', 'DESC')->get(),
                'Histori pengalihan stok berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Belum ada stok yang dialihkan',
                404,
            );
        }    
    }

    public function shiftingStock(Request $request) {
        $shiftStock = $request->all();

        $todayDate = Carbon::now()->toDateString();
        $todayTime = Carbon::now()->format('H:i:s');

        $validate = Validator::make($shiftStock, [
            'product_id' => 'required|exists:products,id,deleted_at,NULL',
            'shiftStockDestination' => 'required|exists:products,id,deleted_at,NULL',
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        //Get product
        $sourceProduct = Product::where('id', '=', $shiftStock['product_id']);
        $destinationProduct = Product::where('id', '=', $shiftStock['shiftStockDestination']);

        $stockSourceBefore = $sourceProduct->value('stock');
        $stockDestBefore = $destinationProduct->value('stock');

        $validate = Validator::make($shiftStock, [
            'quantity' => "required|numeric|max:$stockSourceBefore",
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        $shiftStock['shifting_date'] = $todayDate;
        $shiftStock['shifting_time'] = $todayTime;

        //Store shift stock to database
        //Shift stock source
        $storeShiftSource = ShiftStock::create([
            'product_id' => $shiftStock['product_id'],
            'quantity' => $shiftStock['quantity'],
            'shifting_date' => $shiftStock['shifting_date'],
            'shifting_time' => $shiftStock['shifting_time'],
        ]);
        //Shift stock destination
        $storeShiftDest = ShiftStockDestination::create([
            'product_id' => $shiftStock['shiftStockDestination'],
            'shift_source_id' => $storeShiftSource->id,
            'quantity' => $shiftStock['destQty'],
        ]);

        //Update source product stock (count as out stock)
        $this->reduceProductStock($storeShiftSource->product_id, $storeShiftSource->quantity);

        //Update destination product (count as incoming stock)
        $this->addProductStock($storeShiftDest->product_id, $storeShiftDest->quantity);

        $stockSourceAfter = Product::where('id', '=', $storeShiftSource->product_id)->value('stock');
        $stockDestAfter = Product::where('id', '=', $storeShiftDest->product_id)->value('stock');

        return response([
            'message' => 'Stok berhasil dialihkan',
            'data' => $storeShiftSource->load('shiftStockDestination'),
            'sourceBefore' => $stockSourceBefore,
            'sourceAfter' => $stockSourceAfter,
            'destBefore' => $stockDestBefore,
            'destAfter' => $stockDestAfter,
        ]);
    }

    public function cancelShiftStock($id) {
        $shiftStockData = ShiftStock::with('shiftStockDestination')->findOrFail($id);

        $destinationData = ShiftStockDestination::where('shift_source_id', '=', $id);

        //Add stock to source product
        $this->addProductStock($shiftStockData->product_id, $shiftStockData->quantity);

        //Reduce stock on destination product
        $this->reduceProductStock($destinationData->value('product_id'), $destinationData->value('quantity'));

        //After call this function, shift stock destination will be deleted too cause it's already cascaded on shift stock table
        if($shiftStockData->delete()){
            return ResponseFormatter::success(
                $shiftStockData,
                'Alih stok berhasil dibatalkan',
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Pembatalan alih stok gagal dilakukan',
                400,
            );
        }
    }
}
