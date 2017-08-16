<?php


namespace App;
use App\Bill;
use App\BillMember;
use App\Item;
use App\ItemMember;
use Datetime;
use Exception;

class BillBuilder {


    private $itemsToNotDelete;
    private $itemsMembersToNotDelete;
    private $billMembersToNotDelete;
    
    private function makeItemMember($input){
        if ($input->id != -1) {
            $itemMember = ItemMember::find($input->id);
            array_push($this->itemsMembersToNotDelete, $input->id);
        } else {
            $itemMember = new ItemMember();
        }        
        $itemMember->userId = $input->user->id;
        $itemMember->distribution = $input->distribution;
        return $itemMember;
    }
    
    private function makeItem($input){
        if ($input->id != -1) {
            $item = Item::find($input->id);
            array_push($this->itemsToNotDelete, $input->id);
        } else {
            $item = new Item();
        }
        $item->name = $input->name;
        $item->qt = $input->qt;
        $item->price = $input->price;
        return $item;
    }
    
    private function makeBillMember($input){
        if ($input->id != -1) {
            $member = BillMember::find($input->id);
            $member->paid = $member->paid - ($member->contribution - $input->contribution);
            array_push($this->billMembersToNotDelete, $input->id);
        } else {
            $member = new BillMember();
            $member->paid = $input->contribution;
        }
        $member->userId = $input->user->id;
        $member->value = $input->value;
        $member->contribution = $input->contribution;
        return $member;
    }
    
    private function makeBill($input){
        if($input->id != -1){
            $bill = Bill::find($input->id);
        }else{
            $bill = new Bill();
        }
        $bill->name = $input->name;
        if(isset($input->date)){
            $bill->date = new Datetime($input->date);
        }
        if(isset($input->alertDate)){
            $bill->alertDate = new Datetime($input->alertDate);
        }
        if(isset($input->description)){
            $bill->description = $input->description;
        }
        $bill->groupId = $input->group->id;
        return $bill;
    }
    
    public function deleteleRelationships($bill){
        foreach($bill->members as $member){
            if(!in_array($member->id, $this->billMembersToNotDelete)){
                $member->delete();
            }
        }
        foreach($bill->items as $item){
                if(!in_array($item->id, $this->itemsToNotDelete)){
                    $item->delete();
                }
            foreach($item->members as $member){
                 if(!in_array($member->id, $this->itemsMembersToNotDelete)){
                    $member->delete();
                }   
            }
        }
    }
    
    public function save($billJson){
        $this->billMembersToNotDelete = [];
        $this->itemsMembersToNotDelete = [];
        $this->itemsToNotDelete = [];
        $object = json_decode($billJson);
        $bill = $this->makeBill($object->bill);
        $billMembers = [];
        $totalContribution = 0.0;
        $totalValue = 0.0;
        foreach($object->bill->members as $inputMember){
            $totalContribution = bcadd($totalContribution, $inputMember->contribution, 2);
            $totalValue = bcadd($totalValue, $inputMember->value, 2);
            array_push($billMembers, $this->makeBillMember($inputMember));
        }
        $items = [];
        $itemMembers = [];
        $totalItems = 0.0;
        foreach($object->bill->items as $inputItem){
            $itemValue = bcmul($inputItem->qt, $inputItem->price, 2);
            $totalItems = bcadd($totalItems, $itemValue, 2);
            array_push($items, $this->makeItem($inputItem));
            $members = [];
            $totalDistribution = 0.0;
            foreach($inputItem->members as $inputMember){
                $totalDistribution = bcadd($totalDistribution, $inputMember->distribution, 2);
                array_push($members, $this->makeItemMember($inputMember));
            }
            if($totalDistribution != $itemValue){
                throw new Exception("Fail: value of ".$inputItem->name."(".$itemValue.") != distribution (".$totalDistribution.")");
            }
            array_push($itemMembers, $members);
        }
        if($totalItems != $totalContribution){
            throw new Exception("Fail: items value (".$totalItems.") != contribution value(".$totalContribution.")");
        }else if($totalItems != $totalValue){
            throw new Exception("Fail: items value (".$totalItems.") != member value(".$totalValue.")");
        }
        $bill->total = $totalItems;
        if($object->bill->id != -1){
            $this->deleteleRelationships($bill);
        }
        $bill->save();
        $bill->members()->saveMany($billMembers);
        $bill->items()->saveMany($items);
        for($i = 0; $i < count($items); $i++){
            $items[$i]->members()->saveMany($itemMembers[$i]);
        }
        return $bill;
    }
    
}
