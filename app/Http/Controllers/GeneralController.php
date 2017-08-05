<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Bill;
use App\Requeriment;
class GeneralController extends Controller
{
    
    
    public function index(){
        $user = Auth::user();
        if($user != null){
            $generalInformation = User::getGeneralInformation($user);
            $billsInDebt = Bill::getPendingBills($user->id);            
            $pageInfo = (object) array(
                'billsInDebt' => $billsInDebt,
                'pendingValues' => Bill::getPendingValues($billsInDebt, $user->id),
                'user' => $user,
                'waitingRequirements' => Requeriment::getWaitingRequirements($user->id)
            );
            return view('general')->with('generalInformation', $generalInformation)
                    ->with('pageInfo', $pageInfo);
        }
        return redirect('/');
    }
    
    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
