<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon;

class Bill extends Model {

    //
    protected $table = "bills";

    public function members() {
        return $this->hasMany('App\BillMember', 'billId', 'id');
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
        if($this->alert != null){
            $currentDate = Carbon\Carbon::now();
            return Carbon\Carbon::parse($this->alert) > $currentDate;
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
        foreach ($bill->items as $item) {
            $item->load('members');
            foreach ($item->members as $member) {
                $member->load('user');
            }
        }
        return $bill;
    }

    public static function getBillsInDebtWithUser($receiverId, $paidId) {
        $bills = Bill::select('bills.*')
                ->join('billsMembers as RU', 'RU.billId', '=', 'bills.id')
                ->join('billsMembers as PU', 'PU.billId', '=', 'bills.id')
                ->whereColumn('RU.paid' ,'>','RU.value')
                ->whereColumn('PU.paid','<','PU.value')
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
                        ->whereColumn('BM.paid','!=','BM.value')
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
                    $pendingValues->valueToPay += $member->valueToPay();
                } else if ($member->needToReceiver()) {
                    $pendingValues->valueToReceiver += $member->valueToReceiver();
                }
            }
        }
        return $pendingValues;
    }

    public static function getAlertBills($userId) {
        return Bill::select('bills.*')
                        ->join('billsMembers as BM', 'BM.billId', '=', 'bills.id')
                        ->whereColumn("BM.paid",'!=','BM.value')
                        ->where('BM.userId', '=', $userId)
                        ->where('bills.alertDate', '<', Carbon\Carbon::now())
                        ->get();
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
                        if ($value > $debt) {
                            if (!isset($sugestions[$member->user->toString()])) {
                                $sugestions[$member->user->toString()] = 0.0;
                            }
                            $sugestions[$member->user->toString()] += $debt;
                            $value -= $debt;
                        } else {
                            if (!isset($sugestions[$member->user->toString()])) {
                                $sugestions[$member->user->toString()] = 0.0;
                            }
                            $sugestions[$member->user->toString()] += $value;
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

}
