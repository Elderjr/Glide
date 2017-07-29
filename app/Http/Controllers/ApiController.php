<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;

class ApiController extends Controller {

    
    public function getUserByUsername($username){
        $user = User::getUserByUsername($username);
        if($user != null){
            return $user->toJson();
        } else{
            return "null";
        }
    }
    
    public function getGroupById($id){
        return Group::getGroupById($id);
    }
}
