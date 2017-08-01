<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Group;
use App\GroupMembers;
use Illuminate\Http\Request;
use App\User;
use App\Util;
use App\Feedback;

class GroupController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user = Auth::user();
        if ($user != null) {
            $generalInformation = User::getGeneralInformation($user);
            $myGroups = Group::getGroupsByUserId($user->id);
            $pageInfo = (object) array(
                        'user' => $user,
                        'myGroups' => $myGroups
            );
            return view('gerenciadorGrupos')->with('generalInformation', $generalInformation)
                            ->with('pageInfo', $pageInfo);
        }
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $generalInformation = User::getGeneralInformation(Auth::user());
        return view('cadastrarGrupo')->with('generalInformation', $generalInformation);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $user = Auth::user();
        $object = json_decode($request->get("groupJson"));
        $group = new Group();
        $group->name = $object->name;
        $creatorMember = new GroupMembers();
        $creatorMember->userId = $user->id;
        $creatorMember->admin = true;
        $members = [$creatorMember];
        foreach ($object->members as $memberInput) {
            $member = new GroupMembers();
            $member->userId = $memberInput->user->id;
            $member->admin = $memberInput->admin;
            array_push($members, $member);
        }
        
        $group->save();
        $group->members()->saveMany($members);
        return redirect(action("GroupController@show", $group->id));
    }

    public function setAdminAsTrue(Request $request, $groupId) {
        $feedback = Util::generateFeedbackObject();
        $userId = $request->get("userId");
        Group::setAdmin($groupId, $userId, true);
        $user = User::find($userId);
        $feedback->success = "O usuário " . $user->username . " agora é administrador";
        return back()->with('feedback', $feedback);
    }

    public function setAdminAsFalse(Request $request, $groupId) {
        $feedback = Util::generateFeedbackObject();
        $userId = $request->get("userId");
        Group::setAdmin($groupId, $userId, false);
        $user = User::find($userId);
        $feedback->success = "O usuário " . $user->username . " não é mais administrador";
        return back()->with('feedback', $feedback);
    }

    public function removeMember(Request $request, $groupId) {
        $feedback = Util::generateFeedbackObject();
        $userId = $request->get("userId");
        Group::removeMember($groupId, $userId);
        $user = User::find($userId);
        $feedback->success = "O usuário " . $user->username . " foi removido do grupo";
        if (GroupMembers::where('groupId', '=', $groupId)->count() == 0) {
            return redirect(action("GroupController@index"));
        } else if (GroupMembers::where('groupId', '=', $groupId)->where('admin', '=', true)->count() == 0) {
            $member = GroupMembers::where('groupId', '=', $groupId)->first();
            $member->admin = true;
            $member->save();
        }
        return back()->with('feedback', $feedback);
    }

    public function leaveGroup($groupId) {
        $user = Auth::user();
        if ($user != null) {
            $feedback = Util::generateFeedbackObject();
            $group = Group::find($groupId);
            if ($group != null) {
                Group::removeMember($groupId, $user->id);
                $feedback->success = "Saiu do grupo " . $group->name . " com sucesso";
            }
            return back()->with('feedback', $feedback);
        }
        //TODO: redirect para home
    }

    public function storeMember(Request $request, $groupId) {
        $feedback = Util::generateFeedbackObject();
        $username = $request->get("username");
        $user = User::where("username", "=", $username)->first();
        if ($user != null) {
            $find = GroupMembers::where("userId", "=", $user->id)->where("groupId", "=", $groupId);
            if ($find == null) {
                $groupMember = new GroupMembers();
                $groupMember->userId = $user->id;
                $groupMember->groupId = $groupId;
                $groupMember->admin = false;
                $groupMember->save();
                $feedback->success = $user->name . " adicionado com sucesso";
            } else {
                $feedback->error = $user->name . " já se encontra no grupo";
            }
        } else {
            $feedback->error = "O usuário não encontrado";
        }
        return back()->with('feedback', $feedback);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = Auth::user();
        $feedback = new Feedback();
        if ($user != null) {
            $generalInformation = User::getGeneralInformation($user);
            $group = Group::find($id);
            $group->load('members');
            if ($group->getMemberById($user->id) != null) {
                foreach ($group->members as $member) {
                    $member->load('user');
                }
                $pageInfo = (object) array(
                            'user' => $user,
                            'group' => $group,
                            'isAdmin' => $group->getMemberById($user->id)->admin
                );
                return view('grupoDetalhes')->with('generalInformation', $generalInformation)
                                ->with('pageInfo', $pageInfo);
            }
            $feedback->error = "Você não está cadastrado neste grupo";
            return redirect(action('GroupController@index'))->with('feedback', $feedback);
        }
        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $user = Auth::user();
        $group = Group::find($id);
        $userMember = $group->getMemberById($user->id);
        $feedback = new Feedback();
        if($group != null && $userMember != null){
            if($userMember->admin){
                $object = json_decode($request->get("groupJson"));
                Group::updateName($id, $object->name);
                foreach($object->members as $member){
                    if($member->remove){
                        Group::removeMember($group->id, $member->user->id);
                    }else if($member->add){
                        $groupMember = new GroupMembers();
                        $groupMember->userId = $member->user->id;
                        $groupMember->admin = $member->turnAdmin;
                        $group->members()->save($groupMember);
                    }else if($member->turnAdmin){
                        Group::setAdmin($group->id, $member->user->id, true);
                    }
                }
                $feedback->success = "Alteracoes feitas com sucesso";
            }else{
                $feedback->error = "Voce nao e' admin";
            }
        }elseif($group != null && member == null){
            $feedback->error = "Voce nao e' membro do grupo";
        }else{
            $feedback->error = "Grupo nao encontrado";
        }
        return back()->with('feedback', $feedback);
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
