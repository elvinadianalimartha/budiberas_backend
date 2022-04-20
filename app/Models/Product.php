<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'product_name',
        'size',
        'price',
        'description',
        'stock',
        'stock_status',
        'can_be_retailed',
    ];

    protected static function boot() {
        parent::boot();

        static::deleted(function ($product) {
            $product->productGalleries()->delete();
        });
    }

    public function getDeletedAtAttribute(){
        if(!is_null($this->attributes['deleted_at'])){
            return Carbon::parse($this->attributes['deleted_at'])->format('d/m/Y H:i:s');
        }
    }

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('d/m/Y H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('d/m/Y H:i:s');
        }
    }

    //set relationship with other table
    public function productGalleries() {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id');
    }

    public function incomingStocks(){
        return $this->hasMany(IncomingStock::class, 'product_id', 'id');
    }

    public function outStocks(){
        return $this->hasMany(OutStock::class, 'product_id', 'id');
    }

    // public function carts() {
    //     return $this->hasMany(Cart::class, 'product_id', 'id');
    // }

    // public function transactionDetails() {
    //     return $this->hasMany(TransactionDetail::class, 'product_id', 'id');
    // }

    //FK
    public function productCategory() {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
}
