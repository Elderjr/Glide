@extends('shared.layout')

@section('jsImport')
<script src='{{URL::asset('js/angular.min.js')}}'></script>
<script>
    var app = angular.module('myApp', [], function ($interpolateProvider) {
        $interpolateProvider.startSymbol('@{');
        $interpolateProvider.endSymbol('}');
    });
    app.controller("myCtrl", function ($scope, $http) {
        $scope.pageInfo = JSON.parse('{!!json_encode($pageInfo)!!}');
        $scope.loadMsg = "";
        for(var i = 0; i < $scope.pageInfo.group.members.length; i++){
            $scope.pageInfo.group.members[i].remove = false;
            $scope.pageInfo.group.members[i].turnAdmin = false;
            $scope.pageInfo.group.members[i].add = false;
        }
        
        $scope.searchUser = function () {
            if ($scope.username != ""){
                $scope.loadMsg = "Procurando usuario...";
                alert($scope.username);
                $http.get("{{URL::asset('api/usuario')}}/" + $scope.username).then(addIntegrant);
            }
        }
        
        function existMember(userId){
            for(var i = 0; i < $scope.pageInfo.group.members.length; i++){
                if($scope.pageInfo.group.members[i].user.id == userId){
                    return true;
                }
            }
            return false;
        }
    
    
        function addIntegrant(response) {
            if (response.data != "null") {
                if(!existMember(response.data.id)){
                    var member = {
                        user: response.data,
                        turnAdmin: false,
                        remove: false,
                        add: true
                    };
                    $scope.pageInfo.group.members.push(member);
                    $scope.username = "";
                    $scope.loadMsg = "";
                }else{
                    $scope.loadMsg = "Usuario ja registrado";
                }
            }else {
                $scope.loadMsg = "Usuario nao encontrado";
            }
        }
    });
</script>
@stop
@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Detalhes do Grupo</h3>
    </div>
</div>
<div class="row" ng-app="myApp" ng-controller="myCtrl">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>@{pageInfo.group.name}</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" ng-app="myApp" ng-controller="myCtrl">
                <br />
                <form class="form-vertical form-label-left" action="{{action("GroupController@edit", $pageInfo->group->id)}}" method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="groupJson" value="@{pageInfo.group}" />
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label"> Nome do Grupo: <span class="required"></span></label>
                            <input type="text" required="required" class="form-control" ng-model="pageInfo.group.name" ng-disabled="!pageInfo.isAdmin"required>
                        </div>
                    </div>
                    <br/>
                    <div class="row" ng-show="pageInfo.isAdmin">
                        <div class="col-md-12">
                            <label class="control-label">Membro</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input class="form-control col-md-7 col-xs-12" type="text" ng-model="username">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-block" ng-click="searchUser()">Adicionar</button>
                                </div>
                                <div class="col-md-2">
                                    @{ loadMsg }
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-md-6">
                            <table class="table">
                                <thead>
                                    <th>#</th>
                                    <th>Member</th>
                                    <th ng-if="pageInfo.isAdmin">Tornar Admin</th>
                                    <th ng-if="pageInfo.isAdmin"> Remover</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="member in pageInfo.group.members">
                                        <td>@{pageInfo.group.members.indexOf(member) + 1}</td>
                                        <td>
                                            @{member.user.name} (@{member.user.username})
                                            <span ng-if="member.admin" class="badge bg-blue">Admin</span>
                                        </td>
                                        <td ng-if="pageInfo.isAdmin">
                                            <input ng-if="pageInfo.user.id != member.user.id && !member.admin" type="checkbox" ng-model="member.turnAdmin" />
                                        </td>
                                        <td ng-if="pageInfo.isAdmin">
                                            <input type="checkbox" ng-if="pageInfo.user.id != member.user.id"  ng-model="member.remove"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                    <div class='divider'></div>
                    <div class="form-group" ng-if="pageInfo.isAdmin">
                        <div class="col-md-3 col-md-offset-9">
                            <button type="submit" class="btn btn-block btn-success">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop


