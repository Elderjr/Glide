<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\User;
use App\Payment;
use App\PaymentBill;
use App\BillMember;
class PaymentController extends Controller{
    
    
    public function create(){
        $bills = Bill::getBillsInDebtWithUser(1, 2);
        $receiverUser = User::find(1);
        $payerUser = User::find(2);
        $billsInDebt = [];
        foreach($bills as $bill){
            $simpleBill = (object) array(
                'id' => $bill->id,
                'name' => $bill->name,
                'debt' => $bill->getDebt(1,2),
                'payment' => 0.0
            );
            array_push($billsInDebt, $simpleBill);
        }
        $payement = (object) array(
            'receiverUser' => $receiverUser,
            'payerUser' => $payerUser,
            'bills' => $billsInDebt
        );
        return view('payment')->with('paymentsJson', json_encode($payement));
    }
    
    public function store(Request $request){
        $object = json_decode($request->get("paymentsJson"));
        $payment = new Payment();
        $payment->value = 0.0;
        $payment->payerUserId = $object->payerUser->id;
        $payment->receiverUserId = $object->receiverUser->id;
        $paymentsBills = [];
        foreach($object->bills as $bill){
            $paymentBill = new PaymentBill();
            $paymentBill->billId = $bill->id;
            $paymentBill->value = $bill->payment;
            $payment->value += $paymentBill->value;
            array_push($paymentsBills, $paymentBill);
        }
        $payment->save();
        $payment->paymentBills()->saveMany($paymentsBills);
        $payment->doPayment();
    }
    
    public function rollback($id){
        $generalPayment = Payment::Find($id);
        if($generalPayment != null){
            $generalPayment->rollback();
        }
    }
}
