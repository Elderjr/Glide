<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\PaymentBill;
class Payment extends Model {

    protected $table = "payments";
    protected $casts = [ 'value' => 'float'];
    
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

    public static function registerPaymentFromObjectJson($object, $receiverUserId) {
        $payment = new Payment();
        $payment->status = "confirmed";
        $payment->value = 0.0;
        $payment->payerUserId = $object->payerUser->id;
        $payment->receiverUserId = $receiverUserId;
        $payment->description = (isset($object->description)) ? $object->description : null;
        $paymentBills = [];
        foreach ($object->paymentBills as $input) {
            if ($input->value > 0) {
                $paymentBill = new PaymentBill();
                $paymentBill->billId = $input->bill->id;
                $paymentBill->value = $input->value;
                $payment->value = bcadd($payment->value, $input->value, 2);
                array_push($paymentBills, $paymentBill);
            }
        }
        $payment->save();
        $payment->paymentBills()->saveMany($paymentBills);
        $payment->doPayment();
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
        return $payments->paginate(20, ['*'], 'page', $pag);
    }

}
