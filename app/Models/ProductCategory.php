<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name'
    ];

    protected static function boot() {
        parent::boot();

        static::deleted(function ($category) {
            $category->products()->delete();
            $category->filterCriterias()->delete();
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
    public function products() {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function filterCriterias() {
        return $this->hasMany(FilterCriteria::class, 'category_id', 'id');
    }
}
