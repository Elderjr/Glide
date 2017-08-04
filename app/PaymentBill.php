<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentBill extends Model{
    
    protected $table = "paymentsBills";
    public $timestamps = false;
    
    public function bill(){
        return $this->hasOne('App\Bill', 'id', 'billId');
    }
    
}
