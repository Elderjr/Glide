<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of Util
 *
 * @author elderjr
 */
class Util {
    
    public static function getGeneralInformation(User $user){
        $user->load('myGroups');
        $pendingValues = Bill::getPendingValues($user->id);
        $alertBills = Bill::getAlertBills($user->id);
        return (object) array(
            'user'  => $user,
            'pendingValues' => $pendingValues,
            'alertBills' =>$alertBills
        );
    }
    
    public static function getGeneralInformationByUserId($userId){
        return Util::getGeneralInformation(User::find($userId));
    }
}
