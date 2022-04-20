<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BusinessHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'day',
        'open_time',
        'close_time',
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
    public function shopInfo() {
        return $this->belongsTo(ShopInfo::class, 'shop_id', 'id'); //mungkin business hour perlu belongsto untuk ganti open statusnya
    }
}
