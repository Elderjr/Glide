<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthenticationController extends Controller{
    
    
    public function authenticate(Request $request){
        $username = $request->get("username");
        $password = $request->get("password");
        if($username != null && $password != null){
            $user = User::getUserByUsername($username);
            if($user != null && Hash::check($password, $user->password)){
                return "authentication succeful";
            }
        }
        return back()->with('login_fail', true);
    }
}
