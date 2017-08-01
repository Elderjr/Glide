<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="{{URL::asset('js/angular.min.js')}}">
        </script>
        <script>
            var app = angular.module('myApp', [], function ($interpolateProvider) {
                $interpolateProvider.startSymbol('@{');
                $interpolateProvider.endSymbol('}');
            });
            app.controller("myCtrl", function ($scope, $http) {
                $scope.requeriment = JSON.parse('{!!$requeriment!!}');
            });
        </script>
    </head>
    <body>
        <div ng-app="myApp" ng-controller="myCtrl">
            Requerimento de @{requeriment.sourceUser.name} (@{requeriment.sourceUser.username}) para @{requeriment.destinationUser.name} (@{requeriment.destinationUser.username})
            <br/>
            Valor Requerimento: @{requeriment.value} <br/>
            Dividas em debto:
            <form action="{{action(RequerimentController@accept"))}} method="post">
                <input type="hidden" value="@{requeriment}" />
                <table border="1">
                    <thead>
                    <th>Despesa</th>
                    <th>Divida</th>
                    <th>Pagamento</th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="bill in requeriment.bills">
                            <td>@{bill.name}</td>
                            <td>@{bill.debt}</td>
                            <td>
                                <div ng-if="bill.payment > bill.debt">
                                    Atençao, o pagemento esta maior que a divida
                                </div>
                                <input type="number" step="0.01" ng-model="bill.payment" min="0"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="submit" value="aceitar requerimento" />
            </form>
        </div>
    </body>
</html>
