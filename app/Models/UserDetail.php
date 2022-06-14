<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class UserDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_owner',
        'regency',
        'district',
        'address',
        'address_notes',
        'latitude',
        'longitude',
        'phone_number',
        'default_address',
    ];

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

    public function transactions() {
        return $this->hasMany(Transaction::class, 'user_detail_id', 'id');
    }

    //FK
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
