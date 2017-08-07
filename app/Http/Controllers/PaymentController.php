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

    public function index(Request $request) {
        $user = Auth::user();
        $feedback = new Feedback();
        $generalInformation = User::getGeneralInformation($user);
        if ($user != null && $request->exists("username")) {
            if ($request->username != null) {
                $filterUser = User::getUserByUsername($request->username);
                if ($filterUser != null) {
                    $payments = Payment::filterSearch($user->id, $filterUser->id, $request->date, 1);
                } else {
                    $feedback->error = "Usuario " . $request->username . " nao foi encontrado";
                    return view('payment.payments')->with('generalInformation', $generalInformation)
                                    ->with('feedback', $feedback);
                }
            } else {
                $payments = Payment::filterSearch($user->id, null, $request->date, 1);
            }
            return view('payment.payments')->with('generalInformation', $generalInformation)
                            ->with('payments', $payments);
        } else if ($user != null) {
            $payments = Payment::filterSearch($user->id, null, null, 1);
            return view('payment.payments')->with('generalInformation', $generalInformation)
                    ->with('payments', $payments);
        }
        return redirect('/');
    }

    public function create(Request $request) {
        $user = Auth::user();
        $generalInformation = User::getGeneralInformation($user);
        if ($request->get("username") == null) {
            return view('payment.payment')->with('generalInformation', $generalInformation);
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
                return view('payment.payment')->with('generalInformation', $generalInformation)->with('paymentsJson', $payement);
            } else {
                $feedback = new Feedback();
                $feedback->alert = "Usuário não encontrado";
                return view('payment.payment')->with('generalInformation', $generalInformation)->with('feedback', $feedback);
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

    public function show($id) {
        $user = Auth::user();
        $payment = Payment::find($id);
        return view('payment.paymentDetail')->with('generalInformation', User::getGeneralInformation($user))
                        ->with('payment', $payment);
    }

    public function rollback($id) {
        $user = Auth::user();
        $generalPayment = Payment::Find($id);
        $feedback = new Feedback();
        if ($generalPayment != null) {
            $generalPayment->rollback();
            $feedback->success = "Pagamento revertido com sucesso";
            return redirect(action("PaymentController@index"))->with('generalInformation', User::getGeneralInformation($user))
                            ->with('feedback', $feedback);
        }
        return redirect(action("PaymentController@index"));
    }

}
