<?php

namespace App;
use Illuminate\Support\Facades\Validator;
class JsonValidator{
    
    public static function validateAcceptRequirement($request){
        $validator = Validator::make($request->all(), [
            'acceptedRequirementJson' => 'required|json'
        ]);
        if($validator->fails()){
            return $validator;
        }
        $object = json_decode($request->acceptedRequirementJson, true);
        return Validator::make($object, [
            'requirement' => 'required',
            'requirement.id' => 'required|integer',
            'paymentBills' => 'required|array',
            'paymentBills.*.bill.id' => 'required|integer',
            'paymentBills.*.value' => 'required|numeric'
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
        return Validator::make(json_decode($request->paymentJson, true), [
            'payerUser.id' => 'required|integer',
            'paymentBills' => 'required|array',
            'paymentBills.*.bill.id' => 'required|integer',
            'paymentBills.*.value' => 'required|numeric'
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
            'id' => 'required|integer',
            'name' => 'required|string',
            'date' => 'nullable|date',
            'alertDate' => 'nullable|date',
            'group.id' => 'required|integer',
            'members' => 'required|array',
            'members.*.id' => 'required|integer',
            'members.*.value' => 'required|numeric',
            'members.*.paid' => 'required|numeric',
            'members.*.contribution' => 'required|numeric',
            'members.*.user.id' => 'required|integer',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.qt' => 'required|integer',
            'items.*.members' => 'required|array',
            'items.*.members.*.id' => 'required|integer',
            'items.*.members.*.distribution' => 'required|numeric',
            'items.*.members.*.user.id' => 'required|integer'
        ]);
    }
}