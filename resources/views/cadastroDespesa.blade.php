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
app.filter('itemParticipants', function () {
    return function (allUsers) {
        var integrantsChecked = [];
        for (var i = 0; i < allUsers.length; i++) {
            if (allUsers[i].itemParticipant && allUsers[i].billParticipant) {
                integrantsChecked.push(allUsers[i]);
            }
        }
        return integrantsChecked;
    };
});
app.controller('myCtrl', ['$scope', 'itemParticipantsFilter', function ($scope, itemParticipantsFilter, $http) {
        $scope.myGroups = JSON.parse('{!!$myGroupsJson!!}');
        $scope.groupMembers = [];
        $scope.bill = {};
        $scope.bill.members = [];
        $scope.bill.items = [];
        $scope.addContributor = function () {
            if ($scope.rContributor != null) {
                var contributor = {
                    user: $scope.rContributor.user,
                    contribution: $scope.rContributor.value};
                addElement(contributor, $scope.bill.members);
            }
        }

        $scope.onGroupSelected = function () {
            if ($scope.groupSelected != null) {
                $scope.groupMembers = [];
                for (var i = 0; i < $scope.groupSelected.members.length; i++) {
                    var member = $scope.groupSelected.members[i].user;
                    member.billParticipant = true;
                    member.itemParticipant = true;
                    $scope.groupMembers.push(member);
                    $scope.bill.group = { id: $scope.groupSelected.id,
                        name: $scope.groupSelected.name
                    };
                }
            }
        }

        $scope.addItem = function () {
            var itemMembers = itemParticipantsFilter($scope.groupMembers);
            var item = {name: $scope.rItem.name,
                price: $scope.rItem.price,
                qt: $scope.rItem.qt,
                members: []
            };
            if (itemMembers.length > 0) {
                var dist = makeDistribution(Decimal.mul(item.price, item.qt), itemMembers.length);
                for (var i = 0; i < itemMembers.length; i++) {
                    var member = {
                        user: {id: itemMembers[i].id,
                            name: itemMembers[i].name,
                            username: itemMembers[i].username
                        },
                        distribution: dist[i]};
                    item.members.push(member);
                }
            }
            $scope.bill.items.push(item);
        }

        function addElement(element, array) {
            var index = array.indexOf(element);
            if (index == -1) {
                array.push(element);
            }
        }

        $scope.removeElement = function (element, array) {
            var index = array.indexOf(element);
            if (index >= 0) {
                array.splice(index, 1);
            }
        }

        function makeDistribution(price, n) {
            var dist = [];
            var initialDist = Decimal.div(price, n).floor(2).toNumber();
            for (var i = 0; i < n; i++) {
                dist[i] = initialDist;
            }
            var rest = Decimal.sub(price, Decimal.mul(initialDist, n)).toNumber();
            var index = 0;
            while (rest > 0) {
                dist[index] = Decimal.add(dist[index], 0.01).toNumber();
                rest = Decimal.sub(rest, 0.01).toNumber();
                index = (index + 1) % dist.length;
            }
            return dist;
        }
        $scope.checkDistribution = function checkDistribution(item) {
            if (isNumber(item.price) && isNumber(item.qt)) {
                var total = Decimal.mul(item.price, item.qt).toNumber();
                var sum = 0.0;
                for (var i = 0; i < item.members.length; i++) {
                    if (!isNumber(item.members[i].distribution)) {
                        return false;
                    }
                    sum = Decimal.add(sum, item.members[i].distribution).toNumber();
                }
                return sum == total;
            }
            return false;

        }
        function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }
    }]);
        </script>
    </head>
    <body>
        <div ng-app="myApp" ng-controller="myCtrl">
            JSON: <%bill%>
            <form action="{{action('BillController@store')}}" method="post">
                {{ csrf_field()}}
                <h3>Despesa</h3>
                <input type="hidden" name="billJson" value="<%bill%>" />
                Nome: <input type="text" ng-model="bill.name"/>
                Data: <input type="date" ng-model="bill.date"/>
                Alerta: <input type="date"  ng-model="bill.alertDate"/>
                Grupo: <select ng-model="groupSelected" ng-change="onGroupSelected()" ng-options="group.name for group in myGroups"></select>
                <h4>Usuarios da Despesa</h4>
                <ul>
                    <li ng-repeat="member in groupMembers">
                        <input type="checkbox"  ng-model="member.billParticipant"/> <%member.name%> (<%member.username%>)
                    </li>
                </ul>
                Descriçao:<br/>
                <textarea rows="5" cols="100" ng-model="bill.description">
                    
                </textarea>
                <hr>
                <h3>Contribuintes</h3>
                <select ng-model="rContributor.user">
                    <option ng-repeat="member in groupMembers| filter: {billParticipant: true}" ng-value="member"><%member.name%> (<%member.username%>)</option>
                </select>
                <input type="number" ng-model="rContributor.value" min="0" step=0.01 />
                <button type="button" ng-click="addContributor()">Add</button>
                <table border="1">
                    <thead>
                    <th>Contribuidor</th>
                    <th>Valor</th>
                    <th>Remover</th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="member in bill.members">
                            <td> <%member.user.name%> (<%member.user.username%>) </td>
                            <td> R$ <%member.contribution%> </td>
                            <td>
                                <button type="button" ng-click="removeElement(member, bill.members)">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h3>Items</h3>
                Name: <input type="text" ng-model="rItem.name" />
                Qt: <input type="number" ng-model="rItem.qt" />
                Preço: <input type="number" ng-model="rItem.price" step=0.01 />
                <button type="button" ng-click="addItem()"> Add </button>
                <ul>
                    <li ng-repeat="member in groupMembers| filter: {billParticipant:true}">
                        <input type="checkbox"  ng-model="member.itemParticipant"/> <%member.name%> (<%member.username%>)
                    </li>
                </ul>
                <table border="1">
                    <thead>
                    <th>Item</th>
                    <th>Valor</th>
                    <th>Distribuiçao</th>
                    <th>Remover</th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="item in bill.items">
                            <td>
                                <input type="text" value="<%item.name%>" />
                            </td>
                            <td>
                                <input type="number" ng-model="item.price" step = 0.01 />
                                x
                                <input type="number" ng-model="item.qt"/>
                                (<%item.price * item.qt%>)
                            </td>
                            <td>
                                <div ng-if="!checkDistribution(item)">
                                    A distribuiçao esta incorreta
                                </div>
                                <ul>
                                    <li ng-repeat="member in item.members">
                                        <%member.user.name%> (<%member.user.username%>): <input type="number" ng-model="member.distribution" step=0.01 />
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <button type="button" ng-click="removeElement(item, bill.items)">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="submit" value="cadastrar despesa" />
            </form>
        </div>

    </body>
</html>
