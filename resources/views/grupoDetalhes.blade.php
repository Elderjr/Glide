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
                $http.get("http://localhost:8000/api/usuario/" + $scope.username).then(addIntegrant);
            }
        }
        function addIntegrant(response) {
            if (response.data != "null") {
                var member = {
                    user: response.data,
                    turnAdmin: false,
                    remove: false,
                    add: true
                };
                $scope.pageInfo.group.members.push(member);
                $scope.loadMsg = "";
                $scope.username = "";
            }else {
                $scope.loadMsg = "Usuario nao encontrado";
            }
        }
    });
</script>
@stop
@section('content')
<div class="row" ng-app="myApp" ng-controller="myCtrl">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Detalhes do grupo @{pageInfo.group.name}</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" ng-app="myApp" ng-controller="myCtrl">
                <br />
                <form class="form-horizontal form-label-left" action="{{action("GroupController@edit", $pageInfo->group->id)}}" method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="groupJson" value="@{pageInfo.group}" />
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="groupName"> Nome do Grupo <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="groupName" required="required" class="form-control col-md-7 col-xs-12" ng-model="pageInfo.group.name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Membro
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input class="form-control col-md-7 col-xs-12" type="text" ng-model="username">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" ng-click="searchUser()">Adicionar</button>
                        </div>

                    </div>
                    <div style="text-align: center;">
                        @{ loadMsg }
                    </div>
                    <div class='row'>
                        <div class="col-md-6 col-md-offset-3">
                            <table class="table">
                                <thead>
                                    <th>#</th>
                                    <th>Member</th>
                                    <th>Tornar Admin</th>
                                    <th>Remover</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="member in pageInfo.group.members">
                                        <td>@{pageInfo.group.members.indexOf(member) + 1}</td>
                                        <td>
                                            @{member.user.name} (@{member.user.username})
                                            <span ng-if="member.admin" class="badge bg-blue">Admin</span>
                                        </td>
                                        <td><input ng-if="pageInfo.user.id != member.user.id" type="checkbox" ng-model="member.turnAdmin" /></td>
                                        <td><input type="checkbox" ng-if="pageInfo.user.id != member.user.id"  ng-model="member.remove"/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                    <div class='divider'></div>
                    <div class="form-group">
                        <div class="col-md-2 col-md-offset-5">
                            <button type="submit" class="btn btn-block btn-success">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop


