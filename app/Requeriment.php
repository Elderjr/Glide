<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requeriment extends Model{
    
    protected $table = "requirements";
    
    public function sourceUser(){
        return $this->hasOne('App\User','id','sourceUserId');
    }
    
    public function destinationUser(){
        return $this->hasOne('App\User','id','destinationUserId');
    }
    
}
