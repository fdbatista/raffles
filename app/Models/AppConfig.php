<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    protected $table = 'app_config';
    protected $fillable = ['paypal', 'paypal_fee', 'system_email', 'mail_server', 'mail_port', 'mail_address', 'mail_sender_name', 'mail_username', 'mail_password', 'mail_encryption', 'about_us', 'contact_us', 'max_upload_filesize', 'app_title', 'terms_and_conditions', 'allow_raffle_creation'];
    public $timestamps = false;
}
