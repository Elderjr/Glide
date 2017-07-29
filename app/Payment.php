<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    protected $table = "payments";

    public function paymentBills() {
        return $this->hasMany('App\PaymentBill', 'paymentId', 'id');
    }

    public function payerUser() {
        return $this->hasOne('App\Group', 'id', 'payerUserId');
    }

    public function receiverUser() {
        return $this->hasOne('App\User', 'id', 'receiverUserId');
    }

    public function doPayment() {
        foreach ($this->paymentBills() as $payment) {
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->receiverUserId)
                    ->decrement('paid', $payment->value);
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->payerUserId)
                    ->increment('paid', $payment->value);
        }
    }

    public function rollback($id) {
        foreach ($this->paymentBills() as $payment) {
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->receiverUserId)
                    ->increment('paid', $payment->value);
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->payerUserId)
                    ->decrement('paid', $payment->value);
        }
        $this->delete();
    }

}
