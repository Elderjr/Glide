<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Group;
use App\Bill;
use App\JsonValidator;
use App\BillBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class BillController extends Controller {

    private function createSearchObject(Request $request, $userId){
        return (object) array(
            'myId' => $userId,
            'page' => (isset($request->page)) ? $request->page : 1,
            'billName' => (isset($request->billName)) ? $request->billName : null,
            'billDate' => (isset($request->billDate)) ? $request->billDate : null,
            'billStatus' => (isset($request->billStatus)) ? $request->billStatus : null,
            'billGroupId' => (isset($request->billGroup)) ? null : null
        );
    }
    public function index(Request $request) {
        $user = Auth::user();
        if ($user != null) {
            $generalInformation = User::getGeneralInformation($user);
            $search = $this->createSearchObject($request, $user->id);
            $bills = Bill::filterSearch($search);
            $bills = $bills->appends(Input::except('page'));
            return view('bill.bills')->with('generalInformation', $generalInformation)
                            ->with('myGroups', Group::getGroupsByUserId($user->id))
                            ->with('bills', $bills);
        }
        return redirect('/');
    }

    public function create() {
        $user = Auth::user();
        $myGroupsJson = Group::getGroupsByUserId(Auth::user()->id)->toJson();
        return view('bill.cadastroDespesa')->with('myGroupsJson', $myGroupsJson)
                        ->with('generalInformation', User::getGeneralInformation($user));
    }

    public function store(Request $request) {
        $validator = JsonValidator::validateBill($request);
        if (!$validator->fails()) {
            $builder = new BillBuilder();
            $bill = $builder->save($request->billJson);
            return redirect(action("BillController@show", $bill->id));
        } else {
            return redirect(action("BillController@create"))
                            ->with('feedback', \App\Feedback::feedbackWithErrors($validator->errors()));
        }
    }

    public function show($id) {
        $user = Auth::user();
        $bill = Bill::getCompleteBillById($id);
        return view('bill.billDetails')->with('bill', $bill)
                        ->with('generalInformation', User::getGeneralInformation($user));
    }

    public function edit($id) {
        $user = Auth::user();
        $bill = Bill::getCompleteBillById($id);
        $myGroupsJson = Group::getGroupsByUserId(Auth::user()->id)->toJson();
        return view('bill.editBill')->with('myGroupsJson', $myGroupsJson)
                        ->with('generalInformation', User::getGeneralInformation($user))
                        ->with('bill', $bill);
    }

    public function update(Request $request) {
        $validator = JsonValidator::validateBill($request);
        if (!$validator->fails()) {
            $builder = new BillBuilder();
            $bill = $builder->save($request->billJson);
            return redirect(action("BillController@show", $bill->id));
        } else {
            return redirect(action("BillController@create"))
                            ->with('feedback', \App\Feedback::feedbackWithErrors($validator->errors()));
        }
    }

    public function pendingBills() {
        $user = Auth::user();
        if ($user != null) {
            $generalInformation = User::getGeneralInformation($user);
            $billsInDebt = Bill::getPendingBills($user->id);
            $pageInfo = (object) array(
                        'billsInDebt' => $billsInDebt,
                        'pendingValues' => Bill::getPendingValues($billsInDebt, $user->id),
                        'user' => $user
            );
            return view('bill.despesasPendentes')->with('generalInformation', $generalInformation)
                            ->with('pageInfo', $pageInfo);
        }
        return redirect('/');
    }

}
