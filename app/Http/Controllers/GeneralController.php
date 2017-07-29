<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Bill;
class GeneralController extends Controller
{
    
    
    public function index(){
        $user = Auth::user();
        if($user != null){
            $generalInformation = User::getGeneralInformation($user);
            $pendingValues = Bill::getPendingValues($user->id);
            return view('general')->with('generalInformation', json_encode($generalInformation))
                    ->with('pendingValues', json_encode($pendingValues));
        }
        return redirect('/');
    }
}
