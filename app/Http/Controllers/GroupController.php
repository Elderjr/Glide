<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Group;
use App\GroupMembers;
use Illuminate\Http\Request;
use App\User;
use App\Util;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $user = Auth::user();
        if($user != null){
            $generalInformation = User::getGeneralInformation($user);
            $myGroups = $generalInformation->user->myGroups;
            return view('gerenciadorGrupos')->with('generalInformation', json_encode($generalInformation))
                    ->with('myGroupsJson', json_encode($myGroups));
        }
        return redirect('/');
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
    
    public function removeMember(Request $request){
        $userId = $request->get("userId");
        $groupId = $request->get("groupId");
        Group::removeMember($groupId, $userId);
        return "deletei um membro";
    }
    
    public function leaveGroup($groupId){
        $user = Auth::user();
        if($user != null){
            $feedback = Util::generateFeedbackObject();
            $group = Group::find($groupId);
            if($group != null){
                Group::removeMember($groupId, $user->id);
                $feedback->success = "Saiu do grupo ".$group->name." com sucesso";
            }
            return back()->with('feedback', $feedback);
        }
        //TODO: redirect para home
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
