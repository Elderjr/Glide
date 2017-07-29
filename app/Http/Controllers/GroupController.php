<?php

namespace App\Http\Controllers;

use App\Group;
use App\GroupMembers;
use Illuminate\Http\Request;
use App\User;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cadastrarGrupo');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $group = new Group();
        $group->name = $request->get("name");
        $members = [];
        for($i = 0; $i < count($request->get("memberId")); $i++){
            $members[$i] = new GroupMembers();
            $members[$i]->userId= $request->get("memberId")[$i];
            $members[$i]->admin = $request->get("memberAdmin")[$i] == "true";
        }
        
        $group->save();
        $group->members()->saveMany($members);
        return "cadastrado";
    }

    
    public function setAdminAsTrue($groupId, $userId){
        Group::setAdmin($groupId, $userId, true);
    }
    
    public function setAdminAsFalse($groupId, $userId){
        Group::setAdmin($groupId, $userId, false);
    }
    
    public function removeMember($groupId, $userId){
        Group::removeMember($groupId, $userId);
    }
    
    public function storeMember(){
        $user = User::Find(2);
        $groupMember = new GroupMembers();
        $groupMember->userId = $user->id;
        $groupMember->groupId = 1;
        $groupMember->admin = false;
        $groupMember->save();
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $group = Group::find($id);
        $group->load('members');
        foreach($group->members as $member){
            $member->load('user');
        }
        return $group->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
