<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model{
    
    protected $table = "requirements";
    
    public function sourceUser(){
        return $this->hasOne('App\User','id','sourceUserId');
    }
    
    public function destinationUser(){
        return $this->hasOne('App\User','id','destinationUserId');
    }
    
}
