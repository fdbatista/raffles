<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleUser extends Model
{
    protected $table = 'raffles_users';
    protected $fillable = ['raffle_id', 'user_id', 'chose_number'];
}
