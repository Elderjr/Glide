<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\User;
use App\Payment;
use App\PaymentBill;
use App\Feedback;
use App\JsonValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;


class PaymentController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        $feedback = new Feedback();
        $generalInformation = User::getGeneralInformation($user);
        $page = (isset($request->page)) ? $request->page : 1;
        if ($user != null && $request->exists("username")) {
            if ($request->username != null) {
                $filterUser = User::getUserByUsername($request->username);
                if ($filterUser != null) {
                    $payments = Payment::filterSearch($user->id, $filterUser->id, $request->date, $page);
                } else {
                    $feedback->error = "Usuario " . $request->username . " nao foi encontrado";
                    return view('payment.payments')->with('generalInformation', $generalInformation)
                                    ->with('feedback', $feedback);
                }
            } else {
                $payments = Payment::filterSearch($user->id, null, $request->date, $page);
            }
            $payments = $payments->appends(Input::except('page'));
            return view('payment.payments')->with('generalInformation', $generalInformation)
                            ->with('payments', $payments);
        } else if ($user != null) {
            $payments = Payment::filterSearch($user->id, null, null, $page);
            $payments = $payments->appends(Input::except('page'));
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
                $paymentBills = [];
                foreach ($bills as $bill) {
                    $paymentBill = (object) array(
                        'bill' => (object) array(
                            'id' => $bill->id,
                            'name' => $bill->name,
                            'isInAlert' => $bill->isInAlert(),
                            'debt' => $bill->getDebt($user->id, $payerUser->id)
                        ),
                        'value' => 0.0
                    );
                    array_push($paymentBills, $paymentBill);
                }
                $payment = (object) array(
                    'paymentBills' => $paymentBills,
                    'payerUser' => $payerUser
                );
                return view('payment.payment')->with('generalInformation', $generalInformation)->with('payment', $payment);
            } else {
                $feedback = new Feedback();
                $feedback->alert = "Usuário não encontrado";
                return view('payment.payment')->with('generalInformation', $generalInformation)->with('feedback', $feedback);
            }
        }
    }

    public function store(Request $request) {
        $validator = JsonValidator::validatePaymentRegister($request);
        if (!$validator->fails()) {
            $user = Auth::user();
            $object = json_decode($request->get("paymentJson"));
            Payment::registerPaymentFromObjectJson($object, $user->id);
            $feedback = new Feedback();
            $feedback->success = "Pagamento efetuado com sucesso";
            return back()->with('feedback', $feedback);
        } else {
            $feedback = \App\Feedback::feedbackWithErrors($validator->errors()->all());
            return redirect(action("PaymentController@create"))
                            ->with('feedback', \App\Feedback::feedbackWithErrors($validator->errors()->all()));
        }
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
        if ($generalPayment != null && $generalPayment->status == "confirmed") {
            $generalPayment->rollback();
            $feedback->success = "Pagamento revertido com sucesso";
            return redirect(action("PaymentController@index"))->with('generalInformation', User::getGeneralInformation($user))
                            ->with('feedback', $feedback);
        }
        return redirect(action("PaymentController@index"));
    }

}
