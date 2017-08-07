<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Group;
use App\Bill;
use Illuminate\Http\Request;

class BillController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        if ($user != null) {
            $generalInformation = User::getGeneralInformation($user);
            if ($request->exists("billName")) {
                $bills = Bill::filterSearch($user->id, $request->billName, $request->billDate, null, $request->billStatus, 1);
                return view('bill.bills')->with('generalInformation', $generalInformation)
                                ->with('myGroups', Group::getGroupsByUserId($user->id))
                                ->with('bills', $bills);
            } else {
                $bills = Bill::filterSearch($user->id, null, null, null, null, 1);
                return view('bill.bills')->with('generalInformation', $generalInformation)
                                ->with('myGroups', Group::getGroupsByUserId($user->id))
                                ->with('bills', $bills);
            }
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
        $object = json_decode($request->get("billJson"));
        $object->id = -1;
        $bill = Bill::registerBillFromObjectJson($object);
        return redirect(action("BillController@show", $bill->id));
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
        $object = json_decode($request->get("billJson"));
        $bill = Bill::registerBillFromObjectJson($object);
        return redirect(action("BillController@show", $bill->id));
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
