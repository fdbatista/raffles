<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionMailNotification extends Model
{
    protected $fillable = ['email', 'username', 'transaction_id', 'date', 'tickets', 'amount', 'percent_to_refund'];
}
