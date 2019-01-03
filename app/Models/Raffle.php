<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    public $timestamps = true;
    protected $table = 'raffles';
    protected $fillable = ['id', 'ticket_price', 'first_number', 'last_number', 'min_start_tickets', 'starting_date', 'ending_date', 'product_id', 'user_id'];

    public function product()
    {
        return $this->belongsTo('Models\Product');
    }
    
    public function user()
    {
        return $this->belongsTo('User');
    }
}
