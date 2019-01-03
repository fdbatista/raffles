<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $table = 'roles';
    protected $fillable = ['id', 'name'];
    
    public function users()
    {
        return $this->hasMany('User');
    }
}
