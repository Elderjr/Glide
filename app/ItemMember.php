<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemMember extends Model {

    protected $table = "itemsMembers";
    protected $casts = [ 'distribution' => 'float'];

    public function user() {
        return $this->belongsTo('App\User', 'userId', 'id');
    }

   

}
