<?php

namespace App;
use Illuminate\Support\Facades\Validator;
class JsonValidator{
    
    public static function validateAcceptRequirement($request){
        $validator = Validator::make($request->all(), [
            'requirementJson' => 'required|json'
        ]);
        if($validator->fails()){
            return $validator;
        }
        $requirementJson = json_decode($request->requirementJson, true);
        return Validator::make($requirementJson, [
            'requirement' => 'required',
            'requirement.id' => 'required|integer',
            'payment' => 'required',
            'payment.payerUser.id' => 'required|integer',
            'payment.receiverUser.id' => 'required|integer',
            'payment.paymentBills' => 'required|array',
            'payment.paymentBills.*.bill.id' => 'required|integer',
            'payment.paymentBills.*.value' => 'required|numeric'
        ]);
    }
    
    public static function validateRequirementRegister($request){
        $validator = Validator::make($request->all(), [
            'requirementJson' => 'required|json'
        ]);
        if($validator->fails()){
            return $validator;
        }
        return Validator::make(json_decode($request->requirementJson, true), [
            'requirement' => 'required',
            'requirement.destinationUser.id' => 'required|integer',
            'requirement.sourceUser.id' => 'required|integer',
            'requirement.value' => 'required|numeric',
            'requirement.description' => 'nullable|string'
        ]);
    }
    
    public static function validatePaymentRegister($request){
        $validator = Validator::make($request->all(), [
            'paymentJson' => 'required|json'
        ]);
        if($validator->fails()){
            return $validator;
        }
        return Validator::make(json_decode($request->paymentsJson, true), [
            'payment' => 'required',
            'payment.payerUser.id' => 'required|integer',
            'payment.receiverUser.id' => 'required|integer',
            'payment.paymentBills' => 'required|array',
            'payment.paymentBills.*.bill.id' => 'required|integer',
            'payment.paymentBills.*.value' => 'required|numeric'
        ]);
    }
    
    public static function validateGroupRegister($request){
        $validator = Validator::make($request->all(), [
            'groupJson' => 'required|json'
        ]);
        if($validator->fails()){
            return $validator;
        }
        return Validator::make(json_decode($request->groupJson, true), [
            'group' => 'required',
            'group.name' => 'required|string',
            'group.members' => 'required|array',
            'bill.members.*.admin' => 'required|boolean',
            'bill.members.*.user.id' => 'required|integer'
        ]);
    }
    
    public static function validateGroupEdit($request){
        $validator = Validator::make($request->all(), [
            'groupJson' => 'required|json'
        ]);
        if($validator->fails()){
            return $validator;
        }
        return Validator::make(json_decode($request->groupJson, true), [
            'group' => 'required',
            'group.name' => 'required|string',
            'group.members' => 'required|array',
            'bill.members.*.turnAdmin' => 'required|boolean',
            'bill.members.*.removed' => 'required|boolean',
            'bill.members.*.added' => 'required|boolean',
            'bill.members.*.user.id' => 'required|integer'
        ]);
    }
    
    public static function validateBill($request){
        $validator = Validator::make($request->all(), [
            'billJson' => 'required|json'
        ]);
        if($validator->fails()){
            return $validator;
        }
        return Validator::make(json_decode($request->billJson, true), [
            'bill' => 'required',
            'bill.id' => 'required|integer',
            'bill.name' => 'required|string',
            'bill.date' => 'nullable|date',
            'bill.alertDate' => 'nullable|date',
            'bill.group.id' => 'required|integer',
            'bill.members' => 'required|array',
            'bill.members.*.id' => 'required|integer',
            'bill.members.*.value' => 'required|numeric',
            'bill.members.*.paid' => 'required|numeric',
            'bill.members.*.contribution' => 'required|numeric',
            'bill.members.*.user.id' => 'required|integer',
            'bill.items' => 'required|array',
            'bill.items.*.id' => 'required|integer',
            'bill.items.*.name' => 'required|string',
            'bill.items.*.price' => 'required|numeric',
            'bill.items.*.qt' => 'required|integer',
            'bill.items.*.members' => 'required|array',
            'bill.items.*.members.*.id' => 'required|integer',
            'bill.items.*.members.*.distribution' => 'required|numeric',
            'bill.items.*.members.*.user.id' => 'required|integer',
        ]);
    }
}