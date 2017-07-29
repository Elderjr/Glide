<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Console;

/**
 * Description of Util
 *
 * @author elderjr
 */
class Util {
    
    
    public static function getBla($userId){
        $user = User::find($userId);
        $user->load('myGroups');
        return $user;
    }
}
