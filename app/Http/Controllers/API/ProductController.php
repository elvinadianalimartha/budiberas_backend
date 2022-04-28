<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function getAllProducts(Request $request) {
        $this->updateStatusStock();

        $limit = $request->input('limit'); //untuk pagination
        $categoryId = $request->input('categoryId');
        $stockStatus = $request->input('stockStatus');
        
        //productCategory dan productGalleries ambil dari function yg udah dibuat di model. Nanti outputnya jadi snake_case (product_category, product_galleries)
        $product = Product::with(['productCategory', 'productGalleries']);

        if($categoryId) {
            $product = $product->where('category_id', $categoryId); 
        }

        if($stockStatus) {
            $product = $product->where('stock_status', $stockStatus);
        }

        if($product->count() > 0) {
            return ResponseFormatter::success(
                $product->paginate($limit), //kalo gamau pake pagination, paginatenya ganti get()
                'Data produk berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null, 
                'Data produk tidak ada',
                404,
            );
        }
    }

    //ini cadangan kalo sampe filtering category di get all products gak berhasil
    public function getProductByCategoryName(Request $request) {
        $this->updateStatusStock();

        $limit = $request->input('limit'); //untuk pagination
        $category = $request->input('category');

        //productCategory dan productGalleries ambil dari function yg udah dibuat di model. Nanti outputnya jadi snake_case (product_category, product_galleries)
        $product = Product::with(['productGalleries', 'productCategory'])
                ->whereHas('productCategory', function ($query) use ($category) {
                        $query->where('category_name', '=', $category); //yg dikenain query yg productCategory
                });

        if($product->count() > 0) {
            return ResponseFormatter::success(
                $product->paginate($limit), 
                'Data produk berdasarkan kategori berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null, 
                'Data produk untuk kategori tersebut tidak ada',
                404,
            );
        }
    }

    //untuk list dropdown produk asal saat pengalihan stok
    public function getRetailedProduct(Request $request) {
        $this->updateStatusStock();

        $category = $request->input('category');

        $product = Product::select('product_name')
                    ->where('can_be_retailed', '=', 1);

        if($category) {
            $product = $product->whereHas('productCategory', function ($q) use ($category) { //kalo mau ikut nampilin product categorynya bisa jg pake with
                            $q->where('category_name', '=', $category);
                        });
        }

        if($product->count() > 0) {
            return ResponseFormatter::success(
                $product->get(), 
                'Data produk yang bisa diecer berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null, 
                'Data produk yang bisa diecer tidak ada',
                404,
            );
        }
    }

    public function createProduct(Request $request) {
        $dataProduct = $request->all();

        $validator = Validator::make($dataProduct, [
            'category_id' => 'required|exists:product_categories,id,deleted_at,NULL',
            'product_name' => [
                'required',
                Rule::unique('products')->whereNull('deleted_at')
            ],
            'size' => 'required|numeric',
            'price' => 'required|numeric',
            'description' => 'required',
            'can_be_retailed' => 'required',
            'productGalleries' => 'array',
            'productGalleries.*' => 'image',
        ]);

        if($validator->fails()) {
            return ResponseFormatter::error(
                null,
                $validator->errors(),
                400
            );
        }

        $product = Product::create([
            'category_id' => $dataProduct['category_id'],
            'product_name' => $dataProduct['product_name'],
            'size' => $dataProduct['size'],
            'price' => $dataProduct['price'],
            'description' => $dataProduct['description'],
            'can_be_retailed' => $dataProduct['can_be_retailed'],
        ]);

        $photos = $request->file('productGalleries');

        if($request->hasFile('productGalleries')) {
            foreach($photos as $photo) {
                $photoUrl = $photo->store('assets/product', 'public');

                ProductGallery::create([
                    'product_id' => $product->id,
                    'photo_url' => $photoUrl,
                ]);
            }
        }
        
        return ResponseFormatter::success(
            $product->load('productGalleries'),
            'Data produk berhasil disimpan'
        );
    }

    public function deleteProduct($id) {
        $item = Product::findorFail($id);

        if($item->delete()) {
            return ResponseFormatter::success(
                $item,
                'Data produk berhasil dihapus'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data produk gagal dihapus',
                400,
            );
        }
    }

    public function updateProduct(Request $request, $id) {
        $product = Product::findorFail($id);

        $updateData = $request->all();

        $validate = Validator::make($updateData, [
            'category_id' => 'exists:product_categories,id,deleted_at,NULL',
            'product_name' => [Rule::unique('products')->ignore($product)->whereNull('deleted_at')],
            'size' => 'numeric',
            'price' => 'numeric',
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        //check apakah sudah ada transaksi yg menggunakan product ini
        $productInTransaction = TransactionDetail::where('product_id', '=', $id)->get();

        //kalau blm ada, berarti nama produk masih bisa diganti
        if($productInTransaction->count() == 0) {
            $product->product_name = $updateData['product_name'];
        }
        
        $product->category_id = $updateData['category_id'];
        $product->size = $updateData['size'];
        $product->price = $updateData['price'];
        $product->description = $updateData['description'];
        $product->stock_status = $updateData['stock_status'];

        if($product->stock > 0 && $updateData['stock_status'] == 'Tidak aktif') {
            $product->stock_notes = 'Nonactivate by owner';
        } else {
            $product->stock_notes = null;
        }
        
        $product->can_be_retailed = $updateData['can_be_retailed'];

        if($product->save()) {
            return ResponseFormatter::success(
                $product,
                'Data produk berhasil diedit'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data produk gagal diedit',
                400,
            );
        }
    }

    public function checkProductInTransaction($id) {
        $productInTransaction = TransactionDetail::where('product_id', '=', $id)->get();

        if($productInTransaction->count() > 0) {
            return ResponseFormatter::success(
                $productInTransaction, 
                'Data berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null, 
                'Data tidak ada',
                404,
            );
        }
    }

    public function updateStatusStock() {
        Product::where('stock', '=', 0)
                ->update(['stock_status' => 'Tidak aktif']);
        
        Product::where('stock', '>', 0)
                ->where('stock_notes', '!=', 'Nonactivate by owner')
                ->update(['stock_status' => 'Aktif']);
    }
}
