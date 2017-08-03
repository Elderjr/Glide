<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Group;
use App\Bill;
use App\BillMember;
use App\Item;
use App\ItemMember;
use Illuminate\Http\Request;

class BillController extends Controller {

    public function create() {
        $myGroupsJson = Group::getGroupsByUserId(Auth::user()->id)->toJson();
        return view('cadastroDespesa')->with('myGroupsJson', $myGroupsJson);
    }

    public function store(Request $request) {
        $object = json_decode($request->get("billJson"));
        $bill = new Bill();
        $billMembers = [];
        $items = [];
        $itemMembers = [];

        echo dump($object);

        $bill->name = $object->name;
        if (isset($object->date) && $object->date != "") {
            $bill->date = new DateTime($object->date);
        }
        if (isset($object->alertDate) && $object->alertDate != "") {
            $bill->alertDate = new DateTime($object->alertDate);
        }
        if(isset($object->description)){
            $bill->description = $object->description;
        }
        $bill->groupId = $object->group->id;
        for ($i = 0; $i < count($object->items); $i++) {
            $item = $object->items[$i];
            $items[$i] = new Item();
            $items[$i]->name = $item->name;
            $items[$i]->qt = $item->qt;
            $items[$i]->price = $item->price;
            $bill->total += ($item->qt * $item->price);
            $itemMembers[$i] = [];
            for ($j = 0; $j < count($item->members); $j++) {
                $member = $item->members[$j];
                if (!isset($billMembers[$member->user->id])) {
                    $billMembers[$member->user->id] = new BillMember();
                    $billMembers[$member->user->id]->userId = $member->user->id;
                    $billMembers[$member->user->id]->value = 0.0;
                    $billMembers[$member->user->id]->paid = 0.0;
                    $billMembers[$member->user->id]->contribution = 0.0;
                }
                $billMembers[$member->user->id]->value += $member->distribution;
                $itemMembers[$i][$j] = new ItemMember();
                $itemMembers[$i][$j]->userId = $member->user->id;
                $itemMembers[$i][$j]->distribution = $member->distribution;
            }
        }
        foreach ($object->members as $contributor) {
            if (!isset($billMembers[$contributor->user->id])) {
                $billMembers[$contributor->user->id] = new BillMember();
                $billMembers[$contributor->user->id]->userId = $contributor->user->id;
                $billMembers[$contributor->user->id]->value = 0.0;
                $billMembers[$contributor->user->id]->paid = $contributor->contribution;
                $billMembers[$contributor->user->id]->contribution = $contributor->contribution;
            }
            $billMembers[$contributor->user->id]->contribution += $contributor->contribution;
            $billMembers[$contributor->user->id]->paid += $contributor->contribution;
        }
        $bill->save();
        $bill->members()->saveMany($billMembers);
        $bill->items()->saveMany($items);
        for($i = 0; $i < count($items); $i++){
            $items[$i]->members()->saveMany($itemMembers[$i]);
        }
        return "deu bom";
    }
    
    public function show($id){
        $bill = Bill::getCompleteBillById($id);
        return view('billDetails')->with('billJson', $bill->toJson());
    }
    
    
    public function pendingBills(){
        $user = Auth::user();
        if($user != null){
            $generalInformation = User::getGeneralInformation($user);
            $billsInDebt = Bill::getPendingBills($user->id);            
            $pageInfo = (object) array(
                'billsInDebt' => $billsInDebt,
                'pendingValues' => Bill::getPendingValues($billsInDebt, $user->id),
                'user' => $user
            );
            return view('despesasPendentes')->with('generalInformation', $generalInformation)
                    ->with('pageInfo', $pageInfo);
        }
        return redirect('/');
    }
}
