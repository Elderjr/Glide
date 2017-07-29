<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Requirement;
use App\User;
use App\Bill;

class RequirementController extends Controller {

    
    public function show($id){
        $requirement = Requirement::Find($id);
        $bills = Bill::getBillsInDebtWithUser($requirement->destinationUserId, $requirement->sourceUserId);
        $receiverUser = User::find($requirement->destinationUserId);
        $payerUser = User::find($requirement->sourceUserId);
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
        $payment = (object) array(
            'requeriment' => $requirement,
            'receiverUser' => $receiverUser,
            'payerUser' => $payerUser,
            'bills' => $billsInDebt
        );
        return dump($payment);
    }
    
    
    public function accept(Request $request){
        $object = json_decode($request->get("requirementJson"));
        $requirement = Requeriment::Find($object->$requirement->id);
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
        if($payment->value == $object->$requirement->value){
            $payment->save();
            $payment->paymentBills()->saveMany($paymentsBills);
            $payment->doPayment();
            $requirement->delete();
        } 
        
    }
    
    public function reject($id){
        Requeriment::Find($id)->delete();
    }
    public function store(Request $request) {
        $object = (object) array(
                    'sourceUser' => User::Find(1),
                    'destinationUser' => User::Find(2),
                    'description' => "uma desc",
                    'value' => 5.50
        );
        $requirement = new Requirement();
        $requirement->sourceUserId = $object->sourceUser->id;
        $requirement->destinationUserId = $object->destinationUser->id;
        $requirement->value = $object->value;
        $requirement->description = $object->description;
        $requirement->save();
    }

}
