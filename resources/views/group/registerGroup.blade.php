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
        if ($scope.username != "") {
            $scope.loadMsg = "Procurando usuario...";
            $http.get('{{URL::asset("api/usuario")}}/' + $scope.username).then(addIntegrant);
        }
    }

    function existMember(userId) {
        for (var i = 0; i < $scope.group.members.length; i++) {
            if ($scope.group.members[i].user.id == userId) {
                return true;
            }
        }
        return false;
    }
    function addIntegrant(response) {
        if (response.data != "null") {
            if (!existMember(response.data.id)) {
                var member = {
                    user: response.data,
                    admin: false
                };
                $scope.group.members.push(member);
                $scope.username = "";
                $scope.loadMsg = "";
            } else {
                $scope.loadMsg = "Usuario ja registrado";
            }
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
<div class="page-title">
    <div class="title_left">
        <h3>Cadastro de Grupo</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Formulario</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" ng-app="myApp" ng-controller="myCtrl">
                <br />
                <form class="form-vertical form-label-left" action="{{action("GroupController@store")}}" method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="groupJson" value="@{group}" />
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label"> Nome do Grupo <span class="required">*</span></label>
                            <input type="text" required="required" class="form-control" ng-model="group.name" required />
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label>Membro do grupo:</label>
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input class="form-control col-md-7 col-xs-12" type="text" ng-model="username">
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <button type="button" class="btn btn-primary btn-block" ng-click="searchUser()">Adicionar</button>
                                </div>
                                <div class="col-md-2">
                                    @{ loadMsg }
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-md-6  col-sm-6 col-xs-12">
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
                                        </td>
                                        <td>

                                        </td>
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
                        <div class="col-md-3 col-md-offset-9 col-sm-3 col-sm-offset-9 col-xs-6 col-xs-offset-6">
                            <button type="submit" class="btn btn-block btn-success">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop