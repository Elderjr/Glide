<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requeriment extends Model {

    protected $table = "requirements";

    public function sourceUser() {
        return $this->hasOne('App\User', 'id', 'sourceUserId');
    }

    public function destinationUser() {
        return $this->hasOne('App\User', 'id', 'destinationUserId');
    }

    public function updateToReject(){
        $this->status = "rejected";
        $this->save();
    }
    
    public function updateToAccept(){
        $this->status = "accepted";
        $this->save();
    }
    
    public static function getWaitingRequirements($destinationUserId){
        return Requeriment::where('destinationUserId', $destinationUserId)
                ->where('status','waiting')->get();
    }
    
    public static function getTotalWaitingRequirements($destinationUserId){
        return Requeriment::where('destinationUserId', $destinationUserId)
                ->where('status','waiting')->count();
    }
    
    public static function filterSearch($myId, $userId, $status, $sentOrReceived, $date) {
        $requirements = Requeriment::select('requirements.*');
        if ($sentOrReceived != null && $sentOrReceived == 'sent') {
            $requirements = $requirements->where('sourceUserId', $myId);
            if ($userId != null) {
                $requirements = $requirements->where('destinationUserId', $userId);
            }
        } else if ($sentOrReceived != null && $sentOrReceived == 'received') {
            $requirements = $requirements->where('destinationUserId', $myId);
            if ($userId != null) {
                $requirements = $requirements->where('sourceUserId', $userId);
            }
        } else if ($sentOrReceived == null) {
            $requirements = $requirements->where(function ($query) use ($myId){
                $query->where('sourceUserId', $myId)
                        ->orWhere('destinationUserId', $myId);
            });
            $requirements = $requirements->where(function ($query) use ($userId){
                $query->where('sourceUserId', $userId)
                        ->orWhere('destinationUserId', $userId);
            });
        }
        if ($status != null && in_array($status, ["waiting", "accepted", "rejected"])) {
            $requirements = $requirements->where('status', $status);
        }
        if ($date != null) {
            $requirements = $requirements->where('created_at', '>=', $date);
        }
        return $requirements->get();
    }

}
