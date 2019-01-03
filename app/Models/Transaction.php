<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = ['amount_to_pay', 'amount_to_refund', 'tickets_numbers', 'tickets_count', 'ticket_price', 'user_name', 'user_id', 'raffle_id', 'raffle_owner_id', 'transaction_status_id', 'transaction_description', 'paypal_transaction_id'];
    public $timestamps = true;
}
