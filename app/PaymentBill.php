<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentBill extends Model{
    
    protected $table = "paymentsBills";
    public $timestamps = false;
    protected $casts = [ 'value' => 'float'];
    
    public function bill(){
        return $this->hasOne('App\Bill', 'id', 'billId');
    }
    
    public function generalPayment() {
        return $this->belongsTo('App\Payment', 'paymentId', 'id');
    }
    
}
