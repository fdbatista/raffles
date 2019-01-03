<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'last_name', 'address', 'phone_number', 'email', 'password', 'remember_token', 'api_token', 'activation_token', 'role_id', 'status_id', 'subscribed', 'paypal', 'country_id', 'state_id', 'city', 'zip_code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];
    
    public function products()
    {
        return $this->hasMany('Models\Product');
    }
    
    public function raffles()
    {
        return $this->hasMany('Models\Raffle');
    }
    
    public function role()
    {
        return $this->belongsTo('Models\Role');
    }
    
    public function status()
    {
        return $this->belongsTo('Models\UserStatus');
    }
    
    public function isAdministrator()
    {
        return $this->role_id == 1;
    }
    
    public static function findByApiToken($token)
    {
        return User::where(['api_token' => $token, 'status_id' => 1])->first();
    }
    
    public static function findByActivationToken($token)
    {
        return User::where('activation_token', $token)->first();
    }
    
    public static function findByEmail($email)
    {
        return $user = User::where('email', $email)->first();
    }
    
    public static function findByUsername($username)
    {
        return $user = User::where('username', $username)->first();
    }
    
    public static function findByUsernameOrEmail($usernameOrEmail)
    {
        $user = User::where(['username' => $usernameOrEmail])->first();
        return ($user) ? $user : User::where('email', $usernameOrEmail)->first();
    }
    
    public function confirmEmail()
    {
        $this->status_id = 1;
        $this->activation_token = null;
        $this->save();
    }
}
