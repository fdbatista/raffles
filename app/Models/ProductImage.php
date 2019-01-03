<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public $timestamps = false;
    protected $table = 'product_images';
    protected $fillable = ['product_id', 'image_path'];

    public function product()
    {
        return $this->belongsTo('Models\Product');
    }
}
