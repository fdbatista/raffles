<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    protected $table = 'countries';
    protected $fillable = ['name', 'phone_code', 'flag_path'];
}
