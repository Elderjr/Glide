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
            return bcsub($this->value,$this->paid,2);
        }
        return 0;
    }

    public function needToReceiver() {
        return $this->paid > $this->value;
    }

    public function valueToReceiver() {
        if ($this->needToReceiver()) {
            return bcsub($this->paid,$this->value,2);
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

}
