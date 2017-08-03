<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\User;
use App\Payment;
use App\PaymentBill;
use App\Feedback;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller {

    public function index(){
        
    }
    
    public function create(Request $request) {
        $user = Auth::user();
        $generalInformation = User::getGeneralInformation($user);
        if ($request->get("username") == null) {
            return view('payment')->with('generalInformation', $generalInformation);
        } else {
            $payerUser = User::where('username', $request->get("username"))->first();
            if ($payerUser != null) {
                $bills = Bill::getBillsInDebtWithUser($user->id, $payerUser->id);
                $billsInDebt = [];
                foreach ($bills as $bill) {
                    $simpleBill = (object) array(
                                'id' => $bill->id,
                                'name' => $bill->name,
                                'debt' => $bill->getDebt($user->id, $payerUser->id),
                                'payment' => 0.0
                    );
                    array_push($billsInDebt, $simpleBill);
                }
                $payement = (object) array(
                            'payerUser' => $payerUser,
                            'bills' => $billsInDebt
                );
                return view('payment')->with('generalInformation', $generalInformation)->with('paymentsJson', $payement);
            } else {
                $feedback = new Feedback();
                $feedback->alert = "Usuário não encontrado";
                return view('payment')->with('generalInformation', $generalInformation)->with('feedback', $feedback);
            }
        }
    }

    public function store(Request $request) {
        $user = Auth::user();
        $object = json_decode($request->get("paymentsJson"));
        $payment = new Payment();
        $payment->value = 0.0;
        $payment->payerUserId = $object->payerUser->id;
        $payment->receiverUserId = $user->id;
        $paymentsBills = [];
        foreach ($object->bills as $bill) {
            $paymentBill = new PaymentBill();
            $paymentBill->billId = $bill->id;
            $paymentBill->value = $bill->payment;
            $payment->value += $paymentBill->value;
            array_push($paymentsBills, $paymentBill);
        }
        $payment->save();
        $payment->paymentBills()->saveMany($paymentsBills);
        $payment->doPayment();
        $feedback = new Feedback();
        $feedback->success = "Pagamento efetuado com sucesso";
        return back()->with('feedback', $feedback);
    }

    public function rollback($id) {
        $generalPayment = Payment::Find($id);
        if ($generalPayment != null) {
            $generalPayment->rollback();
        }
    }

}
