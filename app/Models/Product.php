<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = true;
    protected $table = 'products';
    protected $fillable = ['name', 'description', 'quantity', 'condition_id', 'category_id', 'contact_method_id'];
    
    public function raffles()
    {
        return $this->hasMany('Models\Raffle');
    }
    
    public function images()
    {
        return $this->hasMany('Models\ProductImage');
    }
    
    public function category()
    {
        return $this->belongsTo('Models\Category');
    }
    
    public function condition()
    {
        return $this->belongsTo('Models\ProductCondition');
    }
    
    public function user()
    {
        return $this->belongsTo('User');
    }
}
