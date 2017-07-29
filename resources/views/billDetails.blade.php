<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Glide HTML</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="{{URL::asset('js/angular.min.js')}}"></script>
        <script src="{{URL::asset('js/decimal.min.js')}}"></script>
        <script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});
app.controller('myCtrl', ['$scope', function ($scope, $http) {
        $scope.bill = JSON.parse('{!!$billJson!!}');
    }]);
        </script>
    </head>
    <body>
        <div ng-app="myApp" ng-controller="myCtrl">
            <h3>Despesa</h3>
            Nome: <input type="text" ng-model="bill.name" />
            Data: <input type="date" value="<%bill.date%>" />
            Alerta: <input type="date" value="<%bill.alertDate%>"/>
            Grupo:<% bill.group.name%>
            <h4>Usuarios da Despesa</h4>
            <ul>
                <li ng-repeat="member in bill.members">
                    <div ng-if="member.paid > member.value">
                        <% member.user.name %> (<% member.user.username %>) precisa receber: <% member.paid - member.value %>
                    </div>
                    <div ng-if="member.paid < member.value">
                        <% member.user.name %> (<% member.user.username %>) precisa pagar: <% member.value - member.paid %>
                    </div>
                    <div ng-if="member.paid == member.value">
                        <% member.user.name %> (<% member.user.username %>) esta quite
                    </div>
                </li>
            </ul>
            Descriçao:<br/>
            <textarea rows="5" cols="100" ng-model="bill.description">
                    
            </textarea>
            <hr>
            <h3>Contribuintes</h3>
            <table border="1">
                <thead>
                <th>Contribuidor</th>
                <th>Valor</th>
                </thead>
                <tbody>
                    <tr ng-repeat="member in bill.members" ng-if="member.contribution > 0">
                        <td> <%member.user.name%> (<%member.user.username%>) </td>
                        <td> R$ <%member.contribution%> </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <h3>Items</h3>
            <table border="1">
                <thead>
                <th>Item</th>
                <th>Valor</th>
                <th>Distribuiçao</th>
                </thead>
                <tbody>
                    <tr ng-repeat="item in bill.items">
                        <td> <% item.name %></td>
                        <td> <% item.price %> x <% item.qt %> (<%item.price * item.qt%>)</td>
                        <td>
                            <ul>
                                <li ng-repeat="member in item.members">
                                    <%member.user.name%> (<%member.user.username%>): <% member.distribution %>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
