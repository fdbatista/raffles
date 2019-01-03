<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AppConfig;

class Settings extends Model
{
    private static $config;

    public static function getParam($param)
    {
        if (!self::$config)
            self::$config = AppConfig::find(1);
        return self::$config[$param];
    }
}
