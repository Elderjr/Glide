@extends('shared.layout')

@section('jsImport')
<script src='{{URL::asset('js/angular.min.js')}}'></script>
<script>
        var app = angular.module('myApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('@{');
            $interpolateProvider.endSymbol('}');
        });
app.controller("myCtrl", function ($scope, $http) {
    $scope.group = {
        name: "",
        members: []
    };
    $scope.loadMsg = "";
    
    $scope.searchUser = function () {
        if($scope.username != ""){
            $scope.loadMsg = "Procurando usuario...";
            $http.get("http://localhost:8000/api/usuario/" + $scope.username).then(addIntegrant);
        }
    }

    function addIntegrant(response) {
        if (response.data != "null") {
            var member = {
                user: response.data,
                admin: false
            }
            $scope.group.members.push(member);
            $scope.loadMsg = "";
            $scope.username = "";
        } else {
            $scope.loadMsg = "Usuario nao encontrado";
        }
    }

    $scope.removeIntegrant = function (integrant) {
        var index = $scope.group.members.indexOf(integrant);
        if (index >= 0) {
            $scope.group.members.splice(index, 1);
        }
    }
});
</script>
@stop
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Cadastrar Grupo</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" ng-app="myApp" ng-controller="myCtrl">
                <br />
                <form class="form-horizontal form-label-left" action="{{action("GroupController@store")}}" method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="groupJson" value="@{group}" />
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="groupName"> Nome do Grupo <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="groupName" required="required" class="form-control col-md-7 col-xs-12" ng-model="group.name" required>
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
                            <table class='table table-striped'>
                                <thead>
                                <th>Nome</th>
                                <th>Admin</th>
                                <th>Remover</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{Auth::user()->name}} ({{Auth::user()->username}})
                                            <input type="hidden" name="memberId[]" value="{{Auth::user()->id}}" />
                                        </td>
                                        <td>
                                            <input type="checkbox"  ng-model="member.checked"/>
                                        </td>
                                        <td><button class="btn btn-danger btn-sm">remover</button></td>
                                    </tr>
                                    <tr ng-repeat="member in group.members">
                                        <td>
                                            @{member.user.name} (@{member.user.username})
                                        </td>
                                        <td>
                                            <input type="checkbox"  ng-model="member.admin"/>
                                        </td>
                                        <td><button ng-click="removeIntegrant(member)" class="btn btn-danger btn-sm">Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                    <div class='divider'></div>
                    <div class="form-group">
                        <div class="col-md-2 col-md-offset-5">
                            <button type="submit" class="btn btn-block btn-success">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop