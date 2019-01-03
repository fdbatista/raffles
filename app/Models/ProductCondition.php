<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCondition extends Model
{
    public $timestamps = false;
    protected $table = 'product_conditions';
    protected $fillable = ['name'];
    
    public function products()
    {
        return $this->hasMany('Models\Product');
    }
}
