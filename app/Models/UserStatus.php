<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    public $timestamps = false;
    protected $table = 'user_status';
    protected $fillable = ['id', 'name'];
    
    public function users()
    {
        return $this->hasMany('User');
    }
}
