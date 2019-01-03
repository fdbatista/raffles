<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trace extends Model
{
    protected $table = 'traces';
    public $timestamps = false;
    protected $fillable = ['value'];
}