<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

    protected $table = "items";

    public function members() {
        return $this->hasMany('App\ItemMember', 'itemId', 'id');
    }

    private static function register($input, $bill) {
        if ($input->id != -1) {
            $item = Item::find($input->id);
        } else {
            $item = new Item();
        }
        $item->name = $input->name;
        $item->qt = $input->qt;
        $item->price = $input->price;
        $item->billId = $bill->id;
        $item->save();
        return $item;
    }

    private static function deleteRelationships($itemMembersToNotDelete, $item) {
        foreach ($item->members as $member) {
            if (!in_array($member->id, $itemMembersToNotDelete)) {
                $member->delete();
            }
        }
    }

    public static function registerItemFromObjectJson($input, $bill) {
        $itemMembersToNotDelete = [];
        $item = Item::register($input, $bill);
        foreach ($input->members as $inputMember) {
            ItemMember::registerItemMemberFromObjectJson($inputMember, $item);
            if ($inputMember->id != -1) {
                array_push($itemMembersToNotDelete, $inputMember->id);
            }
        }
        if($input->id != -1){
            Item::deleteRelationships($itemMembersToNotDelete, $item);
        }
    }

}
