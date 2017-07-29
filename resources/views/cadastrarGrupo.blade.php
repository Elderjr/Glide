
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Cadastro de Grupo</title>
        <!-- Angular Js -->
        <script src="{{URL::asset('js/angular.min.js')}}">
        </script>
        <script>
            var app = angular.module('myApp', [], function ($interpolateProvider) {
                $interpolateProvider.startSymbol('<%');
                $interpolateProvider.endSymbol('%>');
            });
            app.controller("myCtrl", function ($scope, $http) {
                $scope.members = [];
                $scope.loadMsg = "";
                $scope.searchUser = function (username) {
                    $scope.loadMsg = "Procurando usuario...";
                    $http.get("http://localhost:8000/api/usuario/" + username).then(addIntegrant);
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
    </head>

    <body>

        <div ng-app="myApp" ng-controller="myCtrl">
            <form action="{{action('GroupController@store')}}" method="post">
                {{ csrf_field()}}
                Nome: <input type="text" name="name" />
                Integrantes do grupo:
                <input type="text" ng-model="username" />
                <button type="button" ng-click="searchUser(username)"> add </button>
                <%loadMsg%>
                <table border="1">
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
                                <input type="checkbox" checked/>
                                <input type="hidden" name="memberAdmin[]" value="true" />
                            </td>
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
                <input type="submit" value="cadastrar" />
            </form>
        </div>

    </body>
</html>