<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShiftStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'shifting_date',
        'shifting_time',
        'quantity',
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

    //set relationship with other table
    //FK
    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withTrashed();
    }

    public function shiftStockDestination() {
        return $this->hasOne(ShiftStockDestination::class, 'shift_source_id', 'id');
    }
}
