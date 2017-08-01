@extends('shared.layout')

@section('jsImport')
<script src='{{URL::asset('js/angular.min.js')}}'></script>
<script>
        var app = angular.module('myApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        });
app.controller("myCtrl", function ($scope, $http) {
    $scope.members = [];
    $scope.loadMsg = "";
    $scope.searchUser = function () {
        if($scope.username != ""){
            $scope.loadMsg = "Procurando usuario...";
            $http.get("http://localhost:8000/api/usuario/" + $scope.username).then(addIntegrant);
        }
    }

    function addIntegrant(response) {
        if (response.data != "null") {
            var member = {id: response.data.id,
                name: response.data.name,
                username: response.data.username,
                checked: false
            }
            $scope.members.push(member);
            $scope.loadMsg = "";
            $scope.username = "";
        } else {
            $scope.loadMsg = "Usuario nao encontrado";
        }
    }

    $scope.removeIntegrant = function (integrant) {
        var index = $scope.members.indexOf(integrant);
        if (index >= 0) {
            $scope.members.splice(index, 1);
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
                <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="groupName"> Nome do Grupo <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="groupName" required="required" class="form-control col-md-7 col-xs-12">
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
                        <% loadMsg %>
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
                                            <input type="hidden" name="memberAdmin[]" value="true" />
                                        </td>
                                        <td><button>Remove</button></td>
                                    </tr>
                                    <tr ng-repeat="member in members">
                                        <td>
                                            <%member.name%> (<%member.username%>)
                                            <input type="hidden" name="memberId[]" value="<%member.id%>" />
                                        </td>
                                        <td>
                                            <input type="checkbox"  ng-model="member.checked"/>
                                            <input type="hidden" name="memberAdmin[]" value="<%member.checked%>" />
                                        </td>
                                        <td><button ng-click="removeIntegrant(member)">Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                    <div class='divider'></div>
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button class="btn btn-primary" type="button">Cancel</button>
                            <button class="btn btn-primary" type="reset">Reset</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@stop