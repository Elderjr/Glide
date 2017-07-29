<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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
        return $this->hasOne('App\Group','id','groupId');
    }

    public function getMemberById($id){
        foreach($this->members as $member){
            if($member->userId == $id){
                return $member;
            }
        }
        return null;
    }
    
    public function getDebt($receiverId, $paidId){
        $receiverUser = $this->getMemberById($receiverId);
        $paidUser = $this->getMemberById($paidId);
        if($receiverUser != null && $paidUser != null){            
            if($receiverUser->valueToReceiver() > $paidUser->valueToPay()){
                return $paidUser->valueToPay();
            }else{
                return $receiverUser->valueToReceiver();
            }
        }
        return 0;
    }
    
    public static function getCompleteBillById($id){
        $bill = Bill::find($id);
        $bill->load('group');
        $bill->load('members');
        $bill->load('items');
        foreach($bill->members as $member){
            $member->load('user');
        }
        foreach($bill->items as $item){
            $item->load('members');
            foreach($item->members as $member){
                $member->load('user');
            }
        }
    }
    
    public static function getBillsInDebtWithUser($receiverId, $paidId){
        $bills = Bill::select('bills.*')
            ->join('billsMembers as RU', 'RU.billId', '=', 'bills.id')
            ->join('billsMembers as PU', 'PU.billId', '=', 'bills.id')
            ->whereRaw('RU.paid > RU.value')
            ->whereRaw('PU.paid < RU.value')
            ->where('RU.userId','=','1')
            ->where('PU.userId','=','2')
            ->get();
        foreach($bills as $bill){
            $bill->load('members');
        }
        return $bills;
    }
    
    public static function getPendingBills($userId){
        return Bill::select('bills.*')
            ->join('billsMembers as BM', 'BM.billId', '=', 'bills.id')
            ->whereRaw('BM.paid != BM.value')
            ->where('BM.userId','=',$userId)
            ->get();
        
    }
    
    public static function getPendingValues($userId){
        return DB::table("billsMembers as BM")
            ->select(DB::raw("sum(IF(BM.paid > BM.value,BM.paid - BM.value, 0)) as valueToReceive"),
                    DB::raw("sum(IF(BM.paid < BM.value,BM.value - BM.paid, 0)) as valueToPay"),
                    DB::raw("count(BM.paid != BM.value) as totalPendingBills"))
            ->where("BM.userId",'=',$userId)->first();
    }
    
    public static function getAlertBills($userId){
        return Bill::select('bills.*')
            ->join('billsMembers as BM', 'BM.billId', '=', 'bills.id')
            ->whereRaw('BM.paid != BM.value')
            ->where('BM.userId','=',$userId)
            ->where('bills.alertDate','<', date('yyyy-MM-dddd'))
            ->get();
    }
}
