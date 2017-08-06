<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemMember extends Model {

    protected $table = "itemsMembers";

    public function user() {
        return $this->belongsTo('App\User', 'userId', 'id');
    }

    public static function registerItemMemberFromObjectJson($input, $item) {
        if ($input->id != -1) {
            $itemMember = ItemMember::find($input->id);
        } else {
            $itemMember = new ItemMember();
        }
        $itemMember->itemId = $item->id;
        $itemMember->userId = $input->user->id;
        $itemMember->distribution = $input->distribution;
        $item->members()->save($itemMember);
    }

}
