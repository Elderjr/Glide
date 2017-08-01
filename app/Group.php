<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {

    protected $table = "groups";

    public function members() {
        return $this->hasMany('App\GroupMembers', 'groupId', 'id');
    }

    public function getMemberById($userId) {
        foreach ($this->members as $member) {
            if ($member->userId == $userId) {
                return $member;
            }
        }
        return null;
    }

    public function hasAdmin() {
        foreach ($this->members as $member) {
            if ($member->admin) {
                return true;
            }
        }
        return false;
    }

    public static function getGroupById($id) {
        $group = Group::where('id', $id)->first();
        if ($group != null) {
            $group->load('members');
            for ($i = 0; $i < count($group->members); $i++) {
                $group->members[$i]->load('user');
            }
            return $group->toJson();
        }
        return null;
    }

    public static function getGroupsByUserId($userId) {
        $groups = Group::whereHas('members', function ($query) use($userId) {
                    $query->where('userId', '=', $userId);
                })->get();
        foreach ($groups as $group) {
            $group->load('members');
            foreach ($group->members as $member) {
                $member->load('user');
            }
        }
        return $groups;
    }

    public static function setAdmin($groupId, $userId, $isAdmin) {
        GroupMembers::where('groupId', $groupId)
                ->where('userId', $userId)
                ->update(['admin' => $isAdmin]);
    }


    public static function updateName($groupId, $newName) {
        Group::where('id', $groupId)
                ->update(['name' => $newName]);
    }

    public static function removeMember($groupId, $userId) {
        GroupMembers::where('groupId', $groupId)
                ->where('userId', $userId)
                ->delete();
    }

}
