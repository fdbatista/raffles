<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = true;
    protected $table = 'categories';
    protected $fillable = ['name', 'description'];

    public function products()
    {
        return $this->hasMany('Models\Product');
    }
}
