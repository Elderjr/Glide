<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Bill;

class User extends Model implements Authenticatable{   
    /* attributes:
     * 
     */
    protected $table = "users";
    protected $hidden = ['password'];

    public static function getGeneralInformation(User $user){
        $user->myGroups = User::getGroups($user->id);
        $alertBills = Bill::getAlertBills($user->id);
        return (object) array(
            'user' => $user,
            'alertBills' => $alertBills
        );
    }
    
    public static function getUserByUsername($username){
        return User::where('username',$username)->first();
    }
    
    public static function getUserByEmail($email){
        return User::where('email',$email)->first();
    }
    
    public static function getGroups($userId){
        return Group::join('groupMembers as GM', 'GM.groupId', '=', 'groups.id')
                        ->where('GM.userId', '=', $userId)
                        ->get();
    }

    
    
    public function toString(){
        return $this->name." (".$this->username.")";
    }
    
    //interface methods
    public function getAuthIdentifier() {
        return $this->username;
    }

    public function getAuthIdentifierName() {
        return "username";
    }

    public function getAuthPassword() {
        return $this->password;
    }

    
    public function getRememberToken() {
        
    }

    public function getRememberTokenName() {
        
    }

    public function setRememberToken($value) {
        
    }
}
