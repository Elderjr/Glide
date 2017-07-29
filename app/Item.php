<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    
    protected $table = "items";
    public function members(){
        return $this->hasMany('App\ItemMember', 'itemId', 'id');
    }
}
