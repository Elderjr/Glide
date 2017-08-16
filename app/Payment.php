<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    protected $table = "payments";

    public function paymentBills() {
        return $this->hasMany('App\PaymentBill', 'paymentId', 'id');
    }

    public function payerUser() {
        return $this->hasOne('App\User', 'id', 'payerUserId');
    }

    public function receiverUser() {
        return $this->hasOne('App\User', 'id', 'receiverUserId');
    }

    public function doPayment() {
        foreach ($this->paymentBills as $payment) {
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->receiverUserId)
                    ->decrement('paid', $payment->value);
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->payerUserId)
                    ->increment('paid', $payment->value);
        }
    }

    public function rollback() {
        foreach ($this->paymentBills as $payment) {
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->receiverUserId)
                    ->increment('paid', $payment->value);
            BillMember::where('billId', $payment->billId)
                    ->where('userId', $this->payerUserId)
                    ->decrement('paid', $payment->value);
        }
        $this->status = "canceled";
        $this->save();
    }

    public static function filterSearch($myId, $userId, $date, $pag) {
        $payments = Payment::select('payments.*');
        $payments = $payments->where(function ($query) use ($myId) {
            $query->where('receiverUserId', $myId)
                    ->orWhere('payerUserId', $myId);
        });
        if ($userId != null) {
            $payments = $payments->where(function ($query) use ($userId) {
                $query->where('receiverUserId', $userId)
                        ->orWhere('payerUserId', $userId);
            });
        }
        if ($date != null) {
            $payments = $payments->where('created_at', '>=', $date);
        }
        return $payments->paginate(20, ['*'], 'page', $pag);;
    }

}
