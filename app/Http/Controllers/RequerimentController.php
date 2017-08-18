<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Payment;
use App\PaymentBill;
use App\Requeriment;
use App\User;
use App\Bill;
use App\Feedback;
use App\JsonValidator;
use Illuminate\Support\Facades\Input;

class RequerimentController extends Controller {

    private function createSearchObject(Request $request, $myId, $userId){
        return (object) array(
            'myId' => $myId,
            'page' => (isset($request->page)) ? $request->page : 1,
            'userId' => $userId,
            'status' => (isset($request->status)) ? $request->status : null,
            'sentOrReceived' => (isset($request->sentOrReceived)) ? $request->sentOrReceived : null,
            'date' => (isset($request->date)) ? $request->date : null
        );
    }
    public function index(Request $request) {
        $user = Auth::user();
        if ($user != null) {
            $generalInformation = User::getGeneralInformation($user);
            $userId = null;
            if($request->exists("username") && $request->username != null){
                $user = User::getUserByUsername($request->username);
                if($user != null){
                    $userId = $user->id;
                }else{
                    $feedback = new Feedback();
                    $feedback->error = "Usuario ".$request->username." nao encontrado";
                    return view('requirement.requirements')->with('generalInformation', $generalInformation)
                                    ->with('requirements', [])
                                    ->with('feedback', $feedback);
                }
            }
            $search = $this->createSearchObject($request, $user->id, $userId);
            $requirements = Requeriment::filterSearch($search);
            $requirements = $requirements->appends(Input::except('page'));
            return view('requirement.requirements')->with('generalInformation', $generalInformation)
                            ->with('requirements', $requirements);
        }
        return redirect('/');
    }

    public function create(Request $request) {
        $user = Auth::user();
        $generalInformation = User::getGeneralInformation($user);
        if ($request->get("username") == null) {
            return view('requirement.registerRequirement')->with('generalInformation', $generalInformation);
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
                return view('requirement.registerRequirement')->with('generalInformation', $generalInformation)->with('pageInfo', $pageInfo);
            } else {
                $feedback = new Feedback();
                $feedback->alert = "Usuário não encontrado";
                return view('requirement.registerRequirement')->with('generalInformation', $generalInformation)->with('feedback', $feedback);
            }
        }
    }

    public function show($id) {
        $user = Auth::user();
        if ($user != null) {
            $requirement = Requeriment::find($id);
            $generalInformation = User::getGeneralInformation($user);
            $requirement->load('destinationUser');
            $requirement->load('sourceUser');
            $billsInDebt = Bill::getBillsInDebtWithUser($requirement->destinationUserId, $requirement->sourceUserId);
            $value = $requirement->value;
            $paymentBills = [];
            foreach ($billsInDebt as $bill) {
                $debt = $bill->getDebt($requirement->destinationUserId, $requirement->sourceUserId);
                if ($value > $debt) {
                    $payment = $debt;
                    $value = bcsub($value, $debt, 2);
                } else {
                    $payment = $value;
                    $value = 0;
                }
                $paymentBill = (object) array(
                            'bill' => (object) array(
                                'id' => $bill->id,
                                'name' => $bill->name,
                                'isInAlert' => $bill->isInAlert(),
                                'debt' => $debt
                            ),
                            'value' => $payment
                );
                array_push($paymentBills, $paymentBill);
            }
            $pageInfo = (object) array(
                        'requirement' => $requirement,
                        'userId' => $user->id,
                        'paymentBills' => $paymentBills
            );            
            return view('requirement.requirementDetail')->with('generalInformation', $generalInformation)
                            ->with('pageInfo', $pageInfo);
        }
        return redirect('/');
    }

    public function accept(Request $request) {
        $validator = JsonValidator::validateAcceptRequirement($request);
        if (!$validator->fails()) {
            $object = json_decode($request->get("acceptedRequirementJson"));
            $object->payerUser = (object) array(
                        'id' => $object->requirement->source_user->id
            );
            Payment::registerPaymentFromObjectJson($object, $object->requirement->destination_user->id);
            $requirement = Requeriment::Find($object->requirement->id);
            $requirement->updateToAccept();
            $feedback = new Feedback();
            $feedback->success = "Requerimento aceito com sucesso";
            return redirect(action("RequerimentController@index"))->with('feedback', $feedback);
        } else {
            $feedback = Feedback::feedbackWithErrors($validator->errors()->all());
            return redirect(action("RequerimentController@index"))->with('feedback', $feedback);
        }
    }

    public function reject($id) {
        $user = Auth::user();
        $feedback = new Feedback();
        if ($user != null) {
            $requirement = Requeriment::Find($id);
            if ($requirement != null) {
                $requirement->updateToReject();
                $feedback->success = "Requerimento de " . $requirement->sourceUser->toString() . " rejeitado com sucesso";
            } else {
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
