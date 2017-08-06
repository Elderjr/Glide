<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillMember extends Model {

    protected $table = "billsMembers";

    public function user() {
        return $this->belongsTo('App\User', 'userId', 'id');
    }

    public function needToPay() {
        return $this->paid < $this->value;
    }

    public function valueToPay() {
        if ($this->needToPay()) {
            return $this->value - $this->paid;
        }
        return 0;
    }

    public function needToReceiver() {
        return $this->paid > $this->value;
    }

    public function valueToReceiver() {
        if ($this->needToReceiver()) {
            return $this->paid - $this->value;
        }
        return 0;
    }

    public function isSettled() {
        return $this->value == $this->paid;
    }

    public function getPendingValue() {
        if ($this->needToPay()) {
            return $this->valueToPay();
        } else if ($this->needToReceiver()) {
            return $this->valueToReceiver();
        }
        return 0;
    }

    public static function registerBillMemberFromObjectJson($input, $bill) {
        if ($input->id != -1) {
            $member = BillMember::find($input->id);
            $member->paid = $member->paid - ($member->contribution - $input->contribution);
        } else {
            $member = new BillMember();
            $member->paid = $input->contribution;
        }
        $member->billId = $bill->id;
        $member->userId = $input->user->id;
        $member->value = $input->value;
        $member->contribution = $input->contribution;
        $member->save();
    }

}
