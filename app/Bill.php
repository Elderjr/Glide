<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use DateTime;
use Carbon;

class Bill extends Model {

    
    protected $table = "bills";
    protected $casts = [ 'total' => 'float', 'date' => 'date', 'alertDate' => 'date'    ];

    public function members() {
        return $this->hasMany('App\BillMember', 'billId', 'id');
    }
    
    public function payments() {
        return $this->hasMany('App\PaymentBill', 'billId', 'id');
    }

    public function items() {
        return $this->hasMany('App\Item', 'billId', 'id');
    }

    public function group() {
        return $this->hasOne('App\Group', 'id', 'groupId');
    }
    
    

    public function getMemberById($id) {
        foreach ($this->members as $member) {
            if ($member->userId == $id) {
                return $member;
            }
        }
        return null;
    }

    public function getDebt($receiverId, $paidId) {
        $receiverUser = $this->getMemberById($receiverId);
        $paidUser = $this->getMemberById($paidId);
        if ($receiverUser != null && $paidUser != null) {
            if ($receiverUser->valueToReceiver() > $paidUser->valueToPay()) {
                return $paidUser->valueToPay();
            } else {
                return $receiverUser->valueToReceiver();
            }
        }
        return 0;
    }

    public function getPendingValue($userId) {
        $userMember = $this->getMemberById($userId);
        if ($userMember != null) {
            return $userMember->getPendingValue();
        }
        return 0;
    }

    public function isInAlert() {
        if ($this->alertDate != null) {
            $currentDate = Carbon\Carbon::now();
            return $currentDate > Carbon\Carbon::parse($this->alertDate);
        }
        return false;
    }

    public static function getCompleteBillById($id) {
        $bill = Bill::find($id);
        $bill->load('group');
        $bill->load('members');
        $bill->load('items');
        foreach ($bill->members as $member) {
            $member->load('user');
        }
        foreach ($bill->payments as $payment) {
            $payment->load('generalPayment');
            $payment->generalPayment->load('payerUser');
            $payment->generalPayment->load('receiverUser');
            
        }
        foreach ($bill->items as $item) {
            $item->load('members');
            foreach ($item->members as $member) {
                $member->load('user');
            }
        }
        if($bill->description != null){
            $bill->description = str_replace("\r", "\\r", $bill->description);
            $bill->description = str_replace("\n", "\\n", $bill->description);
        }
        return $bill;
    }

    public static function getBillsInDebtWithUser($receiverId, $paidId) {
        $bills = Bill::select('bills.*')
                ->join('billsMembers as RU', 'RU.billId', '=', 'bills.id')
                ->join('billsMembers as PU', 'PU.billId', '=', 'bills.id')
                ->whereColumn('RU.paid', '>', 'RU.value')
                ->whereColumn('PU.paid', '<', 'PU.value')
                ->where('RU.userId', '=', $receiverId)
                ->where('PU.userId', '=', $paidId)
                ->get();
        foreach ($bills as $bill) {
            $bill->load('members');
        }
        return $bills;
    }

    public static function getPendingBills($userId) {
        return Bill::select('bills.*')
                        ->join('billsMembers as BM', 'BM.billId', '=', 'bills.id')
                        //->whereColumn('BM.paid', '!=', 'BM.value')
                        ->whereRaw('round("BM"."paid") != round("BM"."value")')
                        ->where('BM.userId', '=', $userId)
                        ->get();
    }

    public static function getPendingValues($bills, $userId) {
        $pendingValues = (object) array(
                    'valueToReceiver' => 0.0,
                    'valueToPay' => 0.0
        );
        foreach ($bills as $bill) {
            $member = $bill->getMemberById($userId);
            if ($member != null) {
                if ($member->needToPay()) {
                    $pendingValues->valueToPay = bcadd($pendingValues->valueToPay, $member->valueToPay(),2);
                } else if ($member->needToReceiver()) {
                    $pendingValues->valueToReceiver = bcadd($pendingValues->valueToReceiver,$member->valueToReceiver(),2);
                }
            }
        }
        return $pendingValues;
    }

    public static function getTotalAlertBills($userId) {
        return Bill::select('bills.*')
                        ->join('billsMembers as BM', 'BM.billId', '=', 'bills.id')
                        ->whereColumn("BM.paid", '!=', 'BM.value')
                        ->where('BM.userId', '=', $userId)
                        ->where('bills.alertDate', '<', Carbon\Carbon::now())
                        ->count();
    }

    private static function makeSuggestion($bills, $userId, $receiverSugestion) {
        $sugestions = [];
        foreach ($bills as $bill) {
            $userMember = $bill->getMemberById($userId);
            if (($userMember->needToReceiver() && $receiverSugestion) ||
                    ($userMember->needToPay() && !$receiverSugestion)) {
                $value = $userMember->getPendingValue();
                foreach ($bill->members as $member) {
                    if ($member->userId != $userId) {
                        if ($receiverSugestion) {
                            $debt = $bill->getDebt($userId, $member->userId);
                        } else {
                            $debt = $bill->getDebt($member->userId, $userId);
                        }
                        if ($value > $debt && $debt > 0) {
                            if (!isset($sugestions[$member->user->toString()])) {
                                $sugestions[$member->user->toString()] = 0.0;
                            }
                            $sugestions[$member->user->toString()] = bcadd($sugestions[$member->user->toString()], $debt, 2);
                            $value = bcsub($value,$debt,2);
                        } else if($debt > 0){
                            if (!isset($sugestions[$member->user->toString()])) {
                                $sugestions[$member->user->toString()] = 0.0;
                            }
                            $sugestions[$member->user->toString()] = bcadd($sugestions[$member->user->toString()],$value,2);
                            $value = 0;
                            break;
                        }
                    }
                }
            }
        }
        return $sugestions;
    }

    public static function makeSuggestionToPay($bills, $userId) {
        return Bill::makeSuggestion($bills, $userId, false);
    }

    public static function makeSuggestionToReceiver($bills, $userId) {
        return Bill::makeSuggestion($bills, $userId, true);
    }

    public static function filterSearch($search) {       
        $bills = Bill::select('bills.*')->join('billsMembers as BM', 'BM.billId', '=', 'bills.id')
                ->where('BM.userId', '=', $search->myId);
        if ($search->billName != null) {
            $bills = $bills->where('bills.name','like','%'.$search->billName.'%');
        }
        if ($search->billDate != null) {
            $bills = $bills->where('bills.created_at', '>=', $search->billDate);
        }
        if ($search->billGroupId != null) {
            $bills = $bills->where('bills.groupId', '=', $search->billGroupId);
        }
        if ($search->billStatus != null && in_array($search->billStatus, ["inAlert", "finished", "pending"])) {
            if ($search->billStatus == "inAlert") {
                $bills = $bills->where('bills.alertDate', '>', Carbon\Carbon::now());
                $bills = $bills->whereColumn("BM.paid", "!=", "BM.value");
            } else if ($search->billStatus == "finished") {
                $bills = $bills->whereColumn("BM.paid", "=", "BM.value");
            } else if ($search->billStatus == "pending") {
                $bills = $bills->whereColumn("BM.paid", "!=", "BM.value");
            }
        }
        return $bills->paginate(20, ['*'], 'page', $search->page);
    }

}
