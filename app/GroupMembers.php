<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupMembers extends Model
{
    protected $table = "groupMembers";
    
    public function user(){
        return $this->belongsTo('App\User','userId','id');
    }
    
    public function group(){
        return $this->belongsTo('App\Group','groupId','id');
    }
}
