<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Bill;
use App\Requeriment;
use App\Feedback;

class GeneralController extends Controller {

    public function index() {
        $user = Auth::user();
        if ($user != null) {
            $generalInformation = User::getGeneralInformation($user);
            $billsInDebt = Bill::getPendingBills($user->id);
            $pageInfo = (object) array(
                        'billsInDebt' => $billsInDebt,
                        'pendingValues' => Bill::getPendingValues($billsInDebt, $user->id),
                        'user' => $user,
                        'waitingRequirements' => Requeriment::getWaitingRequirements($user->id)
            );
            return view('general.general')->with('generalInformation', $generalInformation)
                            ->with('pageInfo', $pageInfo);
        }
        return redirect('/');
    }

    public function profile() {
        $user = Auth::user();
        $generalInformation = User::getGeneralInformation($user);
        return view('general.profile')->with('generalInformation', $generalInformation);                        
    }

    
    public function updateProfile(Request $request){
        $user = Auth::user();
        $feedback = new Feedback();
        if($request->name != null && $user->name != $request->name){
            $user->name = $request->name;
            $feedback->success = "Nome alterado com sucesso";
        }
        if($request->email != null && $user->email != $request->email){
            if (User::getUserByEmail($request->email) == null) {
                $user->email = $request->email;
                if ($feedback->success == null) {
                    $feedback->success = "E-mail alterado com sucesso";
                } else {
                    $feedback->success = "Nome e e-mail alterados com sucesso";
                }
            }else{
                $feedback->error = "Este e-mail já está em uso";
            }
        }
        $user->save();
        $generalInformation = User::getGeneralInformation($user);
        return redirect(action("GeneralController@profile"))->with('generalInformation', $generalInformation)
                        ->with('feedback', $feedback);
    }
    
    public function updatePassword(Request $request){
        $user = Auth::user();
        $feedback = new Feedback();
        if($request->oldPassword != null && 
                $request->newPassword != null  && $request->confirmPassword != null){
            if($request->newPassword == $request->confirmPassword){
                if(Hash::check($request->oldPassword, $user->password)){
                    $user->password = Hash::make($request->newPassword);
                    $user->save();
                    $feedback->success = "Senha alterada com sucesso";
                }else{
                    $feedback->error = "Senha atual incorreta";
                }
            }else{
                $feedback->error = "Confirmaçao da senha esta incorreta";
            }
        }else{
            $feedback->error = "Todos os campos de senha devem ser preenchidos";
        }
        $generalInformation = User::getGeneralInformation($user);
        return redirect(action("GeneralController@profile"))->with('generalInformation', $generalInformation)
                        ->with('feedback', $feedback);
    }
    
    
    public function logout() {
        Auth::logout();
        return redirect('/');
    }

}
