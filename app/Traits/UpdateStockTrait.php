<?php

namespace App\Traits;

use App\Models\Product;

trait UpdateStockTrait {
    public function addProductStock($product_id, $quantity) {
        $product = Product::where('id', '=', $product_id);
        $stockBefore = $product->value('stock');
        $addedStock = $stockBefore + $quantity;

        $product->update([
            'stock' => $addedStock
        ]);
        
        if($product->value('stock_notes') != 'Nonactivate by owner') {
            $product->update([
                'stock_status' => 'Aktif'
            ]);
        }
    }

    public function reduceProductStock($product_id, $quantity) {
        $product = Product::where('id', '=', $product_id);
        $stockBefore = $product->value('stock');

        $countReduceStock = $stockBefore - $quantity; 

        $product->update([
            'stock' => $countReduceStock
        ]);
        
        if($product->value('stock') < 1) {
            $product->update([
                'stock_status' => 'Tidak aktif'
            ]);
        }
    }
}