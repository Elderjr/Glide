<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requeriment extends Model {

    protected $table = "requirements";
    protected $casts = [ 'value' => 'float'];
    
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
    
    public static function filterSearch($search) {
        $requirements = Requeriment::select('requirements.*');
        $myId = $search->myId;
        $requirements = $requirements->where(function ($query) use ($myId) {
            $query->where('sourceUserId', $myId)
                    ->orWhere('destinationUserId', $myId);
        });
        if ($search->sentOrReceived != null && $search->sentOrReceived == 'sent') {
            $requirements = $requirements->where('sourceUserId', $search->myId);
            if ($search->userId != null) {
                $requirements = $requirements->where('destinationUserId', $search->userId);
            }
        } else if ($search->sentOrReceived != null && $search->sentOrReceived == 'received') {
            $requirements = $requirements->where('destinationUserId', $search->myId);
            if ($search->userId != null) {
                $requirements = $requirements->where('sourceUserId', $search->userId);
            }
        } else if ($search->sentOrReceived == null) {
            if($search->userId != null){
                $userId = $search->userId;
                $requirements = $requirements->where(function ($query) use ($userId) {
                    $query->where('sourceUserId', $userId)
                            ->orWhere('destinationUserId', $userId);
                });
            }
        }
        if ($search->status != null && in_array($search->status, ["waiting", "accepted", "rejected"])) {
            $requirements = $requirements->where('status', $search->status);
        }
        if ($search->date != null) {
            $requirements = $requirements->where('created_at', '>=', $search->date);
        }
        return $requirements->paginate(20, ['*'], 'page', $search->page);
    }

}
