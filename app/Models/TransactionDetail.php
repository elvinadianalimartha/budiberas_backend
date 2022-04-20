<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id ',
        'quantity',
        'subtotal',
        'order_notes',
    ];

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

    //FK dgn tabel transaction
    public function transaction() {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    //FK dgn tabel product menurut BWA (setiap transaction detail pasti cuma punya 1 produk)
    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    //FK dgn tabel product
    // public function product() {
    //     return $this->hasOne(Product::class, 'product_id', 'id');
    // }

    //FK dgn tabel product awal (kalo product has many trans detail)
    // public function product() {
    //     return $this->belongsTo(Product::class, 'product_id', 'id');
    // }
}
