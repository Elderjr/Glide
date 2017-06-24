<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller {

    public static function buildUser(Request $request) {
        $user = new User();
        $user->name = $request->get("name");
        $user->email = $request->get("email");
        $user->username = $request->get("username");
        $user->password = $request->get("password");
        return $user;
    }

    public static function checkUser(User $user, $confirmPassword) {
        $errors = [];
        if ($user->name == null) {
            array_push($errors, "Campo nome não foi preenchido");
        }
        if ($user->email == null) {
            array_push($errors, "Campo email não foi preenchido");
        } else if (User::getUserByEmail($user->email) != null) {
            array_push($errors, "Email já cadastrado");
        }
        if ($user->username == null) {
            array_push($errors, "Campo username não foi preenchido");
        } else if (User::getUserByUsername($user->username) != null) {
            array_push($errors, "Username já existe");
        }
        if ($user->password == null) {
            array_push($errors, "Campo senha não foi preenchido");
        } else if ($user->password != $confirmPassword) {
            array_push($errors, "Confirmação de senha incorreta");
        }
        return $errors;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    public function store(Request $request) {
        $user = UserController::buildUser($request);
        $errors = UserController::checkUser($user, $request->get("confirmPassword"));
        if(count($errors) == 0){
            $user->password = Hash::make($user->password);
            $user->save();
            return back()->with('register_success', true);
        }else{
            return back()->with('errors',$errors);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
