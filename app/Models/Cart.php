<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'order_notes',
        'is_selected',
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

    //FK dgn tabel product (1 cart cuma punya 1 produk)
    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    //FK dgn tabel user
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
