<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model{   
    
    protected $table = "users";
    
    
    
    public static function getUserByUsername($username){
        return User::where('username',$username)->first();
    }
    
    public static function getUserByEmail($email){
        return User::where('email',$email)->first();
    }
}
