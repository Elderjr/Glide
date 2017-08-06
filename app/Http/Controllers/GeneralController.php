<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function profile(Request $request) {
        $user = Auth::user();
        $feedback = new Feedback();
        if ($user != null) {
            if ($request->exists("name")) {
                if ($request->name != $user->name) {
                    $user->name = $request->name;
                    $user->save();
                    $feedback->success = "Nome alterado com sucesso";
                }
            }
            if ($request->exists("email")) {
                if ($request->email != $user->email) {
                    if (User::getUserByEmail($request->email) == null) {
                        $user->email = $request->email;
                        $user->save();
                        if($feedback->success == null)
                            $feedback->success = "E-mail alterado com sucesso";
                        else
                            $feedback->success = "Nome e e-mail alterados com sucesso"; 
                    } else {
                        $feedback->error = "Este e-mail já está em uso";
                    }
                }
            }
            $generalInformation = User::getGeneralInformation($user);
            return view('general.profile')->with('generalInformation', $generalInformation)
                            ->with('feedback', $feedback);
        }
        return redirect('/');
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

}
