<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthenticationController extends Controller {

    public function authenticate(Request $request) {
        $username = $request->get("username");
        $password = $request->get("password");
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = new User();
            $user->username = $username;
            $user->password = $password;
            Auth::login($user);
            return redirect(action("GeneralController@index"));
        }
        return back()->with('login_fail', true);
    }

}
