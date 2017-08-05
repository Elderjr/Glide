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
        $generalInformation = User::getGeneralInformation($user);
        if ($user != null && $request->exists("username")) {
            if ($request->username != null) {
                $filterUser = User::getUserByUsername($request->username);
                if ($filterUser != null) {
                    $requirements = Requeriment::filterSearch($user->id, $filterUser->id, $request->status, $request->sentOrReceived, $request->date);
                } else {
                    $feedback->error = "Usuario " . $request->username . " nao foi encontrado";
                    return view('requirements')->with('generalInformation', $generalInformation)
                                    ->with('feedback', $feedback);
                }
            } else {
                $requirements = Requeriment::filterSearch($user->id, null, $request->status, $request->sentOrReceived, $request->date);
            }
            return view('requirements')->with('generalInformation', $generalInformation)
                            ->with('requirements', $requirements);
        } else if ($user != null) {
            return view('requirements')->with('generalInformation', $generalInformation);
        }
        return redirect('/');
    }

    public function create(Request $request) {
        $user = Auth::user();
        $generalInformation = User::getGeneralInformation($user);
        if ($request->get("username") == null) {
            return view('registerRequirement')->with('generalInformation', $generalInformation);
        } else {
            $destinationUser = User::where('username', $request->get("username"))->first();
            if ($destinationUser != null) {
                $bills = Bill::getBillsInDebtWithUser($destinationUser->id, $user->id);
                $billsInDebt = [];
                $total = 0.0;
                foreach ($bills as $bill) {
                    $debt = $bill->getDebt($destinationUser->id, $user->id);
                    $total += $debt;
                    $simpleBill = (object) array(
                                'id' => $bill->id,
                                'name' => $bill->name,
                                'debt' => $debt
                    );
                    array_push($billsInDebt, $simpleBill);
                }
                $pageInfo = (object) array(
                            'billsInDebt' => $billsInDebt,
                            'destinationUser' => $destinationUser,
                            'total' => $total
                );
                return view('registerRequirement')->with('generalInformation', $generalInformation)->with('pageInfo', $pageInfo);
            } else {
                $feedback = new Feedback();
                $feedback->alert = "Usuário não encontrado";
                return view('registerRequirement')->with('generalInformation', $generalInformation)->with('feedback', $feedback);
            }
        }
    }

    public function show($id) {
        $user = Auth::user();
        if ($user != null) {
            $req = Requeriment::find($id);
            $generalInformation = User::getGeneralInformation($user);
            return view('requirement')->with('generalInformation', $generalInformation)
                            ->with('requirement', $req);
        }
        return redirect('/');
    }

    public function showAccept($id) {
        $user = Auth::user();
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
        $pageInfo = (object) array(
                    'requirement' => $requeriment,
                    'bills' => $simpleBills
        );
        return view('acceptRequeriment')->with('generalInformation', User::getGeneralInformation($user))
                        ->with('pageInfo', $pageInfo);
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
        $user = Auth::user();
        $feedback = new Feedback();
        if ($user != null) {
            $requirement = Requeriment::Find($id);
            if ($requirement != null) {
                $requirement->updateToReject();
                $feedback->success = "Requerimento de ".$requirement->sourceUser->toString()." rejeitado com sucesso";
            }else{
                $feedback->error = "Requerimento nao encontrado";
            }
            return back()->with('feedback', $feedback);
        }
    }

    public function store(Request $request) {
        $user = Auth::user();
        $feedback = new Feedback();
        $requerimentUser = User::find($request->get("destinationUserId"));
        $requeriment = new Requeriment();
        $requeriment->status = "waiting";
        $requeriment->sourceUserId = $user->id;
        $requeriment->destinationUserId = $requerimentUser->id;
        $requeriment->value = $request->get("requirementValue");
        $requeriment->description = $request->get("requirementDescription");
        $requeriment->save();
        $feedback->success = "Requerimento foi enviado com sucesso para " . $requerimentUser->toString();
        return redirect(action('RequerimentController@index'))->with('feedback', $feedback);
    }

}
