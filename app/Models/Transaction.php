<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_detail_id',
        'shipping_rate_id',
        'invoice_code',
        'shipping_type',
        'total_price',
        'transaction_status',
        'payment_method',
        'pickup_code',
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

    public function transactionDetails() {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
    }

    //FK dgn tabel userDetail
    public function userDetail() {
        return $this->belongsTo(UserDetail::class, 'user_detail_id', 'id');
    }

    //FK dgn tabel shippingRate (setiap pesanan cm punya 1 shipping rate?)
    public function shippingRate() {
        return $this->hasOne(ShippingRate::class, 'id', 'shipping_rate_id');
    }
}
