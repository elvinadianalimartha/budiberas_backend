<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function getAllCategories() {
        $category = ProductCategory::all();
        if($category->count() > 0) {
            return ResponseFormatter::success(
                $category, 
                'Data kategori berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null, 
                'Data kategori tidak ada',
                404,
            );
        }
    }

    public function createCategory(Request $request) {
        $dataCategory = $request->all();
        $validator = Validator::make($dataCategory, [
            'category_name' => [
                'required',
                Rule::unique('product_categories')->whereNull('deleted_at')
            ],
        ]);

        if($validator->fails()) {
            return ResponseFormatter::error(
                null,
                $validator->errors(),
                400
            );
        }

        $validatedCategory = $validator->validated();
        $category = ProductCategory::create($validatedCategory);
        
        return ResponseFormatter::success(
            $category,
            'Data produk berhasil disimpan'
        );
    }

    public function deleteCategory($id)
    {
        $item = ProductCategory::findorFail($id);

        if($item->delete()) {
            return ResponseFormatter::success(
                $item,
                'Data kategori berhasil dihapus'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data kategori gagal dihapus',
                400,
            );
        }
    }

    public function updateCategory(Request $request, $id)
    {
        $category = ProductCategory::findorFail($id);

        $updateData = $request->all();

        $validate = Validator::make($updateData, [
            'category_name' => [Rule::unique('product_categories')->ignore($category)->whereNull('deleted_at')]
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        $category->category_name = $updateData['category_name'];

        if($category->save()) {
            return ResponseFormatter::success(
                $category,
                'Data kategori berhasil diedit'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data kategori gagal diedit',
                400,
            );
        }
    }
}
