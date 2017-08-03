<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Requeriment;
use App\User;
use App\Bill;
use App\Feedback;

class RequerimentController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        $feedback = new Feedback();
        if ($user != null && $request->get("search")) {
            if ($request->get("username") != null) {
                $filterUser = User::getUserByUsername($request->get("username"));
                if ($filterUser != null) {
                    $requirements = Requeriment::filterSearch($user->id, $filterUser->id, $request->get("status"), $request->get("sentOrReceived"), $request->get("date"));
                    //return view('requeriments')->with('requeriments');
                } else {
                    $feedback->error = "Usuario " . $request->get("username") . " nao foi encontrado";
                    return view('requirements')->with('feedback', $feedback);
                }
            } else {
                $requirements = Requeriment::filterSearch($user->id, null, $request->get("status"), $request->get("sentOrReceived"), $request->get("date"));
            }
            return view('requirements')->with('requeriments', $requirements);
        } else if ($user != null) {
            return view('requirements');
        }
        return redirect('/');
    }

    public function create(Request $request) {
        $user = Auth::user();
        if ($request->get("username") == null) {
            return view('registerRequeriment');
        } else {
            $requerimentUser = User::getUserByUsername($request->get("username"));
            if ($requerimentUser != null) {
                $billsInDebt = Bill::getBillsInDebtWithUser($requerimentUser->id, $user->id);
                $simpleBills = [];
                $total = 0.0;
                foreach ($billsInDebt as $bill) {
                    $debt = $bill->getDebt($requerimentUser->id, $user->id);
                    $simpleBill = (object) array(
                                'id' => $bill->id,
                                'name' => $bill->name,
                                'debt' => $debt,
                                'payment' => 0.0
                    );
                    $total += $debt;
                    array_push($simpleBills, $simpleBill);
                }
                $requeriment = (object) array(
                            'user' => $requerimentUser,
                            'bills' => $simpleBills,
                            'total' => $total
                );
                return view('registerRequeriment')->with('requerimentJson', json_encode($requeriment));
            } else {
                return view('registerRequeriment');
            }
        }
    }

    public function show($id) {
        
    }

    public function showAccept($id) {
        $requeriment = Requeriment::find($id);
        $billsInDebt = Bill::getBillsInDebtWithUser($requeriment->destinationUserId, $requeriment->sourceUserId);
        $simpleBills = [];
        $value = $requeriment->value;
        foreach ($billsInDebt as $bill) {
            $debt = $bill->getDebt($requeriment->destinationUserId, $requeriment->sourceUserId);
            if ($value > $debt) {
                $payment = $debt;
                $value -= $debt;
            } else {
                $payment = $value;
                $value = 0;
            }
            $simpleBill = (object) array(
                        'id' => $bill->id,
                        'name' => $bill->name,
                        'debt' => $debt,
                        'payment' => $payment
            );
            array_push($simpleBills, $simpleBill);
        }
        $requerimentObject = (object) array(
                    'id' => $requeriment->id,
                    'destinationUser' => $requeriment->destinationUser,
                    'sourceUser' => $requeriment->sourceUser,
                    'value' => $requeriment->value,
                    'bills' => $simpleBills
        );
        return view('acceptRequeriment')->with('requeriment', json_encode($requerimentObject));
    }

    public function accept(Request $request) {
        $object = json_decode($request->get("requirementJson"));
        $requirement = Requeriment::Find($object->$requirement->id);
        $payment = new Payment();
        $payment->value = 0.0;
        $payment->payerUserId = $object->destinationUser->id;
        $payment->receiverUserId = $object->receiverUser->id;
        $paymentsBills = [];
        foreach ($object->bills as $bill) {
            $paymentBill = new PaymentBill();
            $paymentBill->billId = $bill->id;
            $paymentBill->value = $bill->payment;
            $payment->value += $paymentBill->value;
            array_push($paymentsBills, $paymentBill);
        }
        if ($payment->value == $object->$requirement->value) {
            $payment->save();
            $payment->paymentBills()->saveMany($paymentsBills);
            $payment->doPayment();
            $requirement->updateToAccept();
        }
    }

    public function reject($id) {
        Requeriment::Find($id)->updateToReject();
    }

    public function store(Request $request) {
        $user = Auth::user();
        $feedback = new Feedback();
        $requerimentUser = User::find($request->get("requerimentUserId"));
        $requeriment = new Requirement();
        $requeriment->status = "waiting";
        $requeriment->sourceUserId = $user->id;
        $requeriment->destinationUserId = $requerimentUser->id;
        $requeriment->value = $request->get("requerimentValue");
        $requeriment->description = $request->get("requerimentDescription");
        $requeriment->save();
        $feedback->success = "Requerimento foi enviado com sucesso para " . $requerimentUser->toString();
        return redirect(action('RequerimentController@index'))->with('feedback', $feedback);
    }

}
