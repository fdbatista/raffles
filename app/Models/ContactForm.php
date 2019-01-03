<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    public $timestamps = false;
    protected $fillable = ['is_auth', 'name', 'email', 'subject', 'body'];
}
