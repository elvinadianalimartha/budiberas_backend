<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShopInfo extends Model
{
    use HasFactory;

    public $table = "shop_info";

    protected $fillable = [
        'email',
        'password',
        'fcm_token',
        'shop_regency',
        'shop_district',
        'shop_address',
        'address_notes',
        'latitude',
        'longitude',
        'phone_number',
        'open_status'
    ];

    public function getCreatedAtAttribute() {
        if(!is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('d/m/Y H:i:s');
        };
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('d/m/Y H:i:s');
        }
    }

    //set relationship with other table
    public function businessHours() {
        return $this->hasMany(BusinessHour::class, 'shop_id', 'id');
    }

    public function shippingRates() {
        return $this->hasMany(ShippingRate::class, 'shop_id', 'id');
    }

}
