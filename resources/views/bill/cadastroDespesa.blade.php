@extends('shared.layout')

@section('title') Cadastro de Despesa @stop

@section('cssImport')
<link href="{{URL::asset('css/pnotify.custom.min.css')}}" rel="stylesheet">
@stop
@section('jsImport')
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
app.controller('myCtrl', ['$scope', 'itemParticipantsFilter', '$http', function ($scope, itemParticipantsFilter, $http) {
        $scope.myGroups = JSON.parse('{!!$myGroupsJson!!}');
        $scope.bill = {id: -1};
        $scope.bill.members = [];
        $scope.bill.items = [];
        $scope.bill.group = null;
        $scope.totalItems = 0.0;
        $scope.step = 1;
        $scope.searchUser = function () {
            if ($scope.username != "") {
                $scope.loadMsg = "Procurando usuario...";
                $http.get("{{URL::asset('api/usuario')}}/" + $scope.username).then(addIntegrant);
            }
        }


        function existsAtLeastOneBillMember() {
            for (var i = 0; i < $scope.bill.members.length; i++) {
                if ($scope.bill.members[i].billParticipant) {
                    return true;
                }
            }
            return false;
        }

        function showMsgError(msgError) {
            new PNotify({
                title: 'Notificaçao de erro',
                text: msgError,
                type: 'error'
            });
        }
        $scope.validateFirstStep = function () {
            var msgError = null;
            if($scope.bill.name == null){
                msgError = "Nome da despesa deve ser preenchido";
            }else if($scope.bill.group == null){
                msgError = "Selecione um grupo para a despesa";
            }else if(!existsAtLeastOneBillMember()){
                msgError = "Selecione pelo menos um integrante da despesa";
            }
            if(msgError != null){
                showMsgError(msgError)
                return false;
            }
            $scope.step = 2;
            return true;
        }

        $scope.validateSecondStep = function () {
            var msgError = null;
            if ($scope.bill.items.length == 0) {
                msgError = 'registre pelo menos um item';
            } else {
                $scope.totalItems = 0.0;
                for (var i = 0; i < $scope.bill.items.length && msgError == null; i++) {
                    if ($scope.bill.items[i].name == "") {
                        msgError = 'O item ' + (i + 1) + 'esta sem nome';
                    } else if (!isNumber($scope.bill.items[i].price)) {
                        msgError = 'O preço do item ' + $scope.bill.items[i].name + " esta com formato errado";
                    } else if (!isNumber($scope.bill.items[i].qt)) {
                        msgError = 'A quantidade do item ' + $scope.bill.items[i].name + " esta com formato errado";
                    } else if (!$scope.checkDistribution($scope.bill.items[i])) {
                        msgError = 'O item ' + $scope.bill.items[i].name + " esta com distribuiçao errada";
                    }
                    var total = Decimal.mul($scope.bill.items[i].price, $scope.bill.items[i].qt).toNumber();
                    $scope.totalItems = Decimal.add($scope.totalItems, total).toNumber();
                }
            }
            if (msgError != null) {
                showMsgError(msgError);
                return false;
            } 
            calculeValuePerMember();
            $scope.step = 3;
            return true;
        }

        $scope.validateThirdStep = function () {
            var msgError = null;
            var total = 0.0;
            var existAtLeastOneContributor;
            for (var i = 0; i < $scope.bill.members.length && msgError == null; i++) {
                if ($scope.bill.members[i].contributor) {
                    existAtLeastOneContributor = true;
                }
                if (!isNumber($scope.bill.members[i].contribution)) {
                    msgError = 'Contribuiçao do usuario ' + $scope.bill.members[i].user.name + " esta no formato errado";
                }
                total = Decimal.add(total, $scope.bill.members[i].contribution).toNumber();
            }
            if (msgError != null) {
                showMsgError(msgError);
                return false;
            } else if (!existAtLeastOneContributor) {
                showMsgError('Registre pelo menos um contribuidor');
                return false;
            } else if (total != $scope.totalItems) {
                showMsgError('Total de contribuiçao diferente do total da despesa');
                return false;
            } else {
                return true;
            }
        }

        function addIntegrant(response) {
            if (response.data != "null") {
                var user = response.data;
                addMember(user, 0.0);
                $scope.loadMsg = "";
                $scope.username = "";
            } else {
                $scope.loadMsg = "Usuario nao encontrado";
            }
        }


        function addMember(user) {
            if (!existMember(user.id)) {
                var member = {
                    id: -1,
                    user: user,
                    contribution: 0.0,
                    paid: 0.0,
                    value: 0.0,
                    itemParticipant: true,
                    billParticipant: true,
                    contributor: false
                };
                $scope.bill.members.push(member);
            }
        }

        $scope.setStep = function (step) {
            $scope.step = step;
        }

        $scope.addContributor = function () {
            if ($scope.rContributor.member != null && $scope.rContributor.value != null) {
                var member = getMemberById($scope.rContributor.member.user.id);
                member.contribution = $scope.rContributor.value;
                member.contributor = true;
            }
        }

        $scope.removeContributor = function (member) {
            member.contributor = false;
            member.contribution = 0.0;
        }

        $scope.onGroupSelected = function () {
            if ($scope.groupSelected != null) {
                $scope.bill.members = [];
                for (var i = 0; i < $scope.groupSelected.members.length; i++) {
                    addMember($scope.groupSelected.members[i].user);
                    $scope.bill.group = {id: $scope.groupSelected.id,
                        name: $scope.groupSelected.name
                    };
                }
            }
        }


        function getMemberById(userId) {
            for (var i = 0; i < $scope.bill.members.length; i++) {
                if ($scope.bill.members[i].user.id == userId) {
                    return $scope.bill.members[i];
                }
            }
            return null;

        }

        function existMember(userId) {
            return (getMemberById(userId) != null);
        }

        $scope.addItem = function () {
            var itemMembers = itemParticipantsFilter($scope.bill.members);
            var item = {
                id: -1,
                name: $scope.rItem.name,
                price: $scope.rItem.price,
                qt: $scope.rItem.qt,
                members: []
            };
            if (itemMembers.length > 0) {
                var dist = makeDistribution(Decimal.mul(item.price, item.qt), itemMembers.length);
                for (var i = 0; i < itemMembers.length; i++) {
                    var member = {
                        id: -1,
                        user: itemMembers[i].user,
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


        function calculeValuePerMember() {
            //reset values
            for (var i = 0; i < $scope.bill.members.length; i++) {
                $scope.bill.members[i].value = 0.0;
            }
            for (var i = 0; i < $scope.bill.items.length; i++) {
                for (var j = 0; j < $scope.bill.items[i].members.length; j++) {
                    var member = getMemberById($scope.bill.items[i].members[j].user.id);
                    if (isNumber($scope.bill.items[i].members[j].distribution)) {
                        member.value = Decimal.add(member.value, $scope.bill.items[i].members[j].distribution);
                    }
                }
            }
        }

        function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }
    }]);

function validateBillForm() {
    return angular.element($("#billForm")).scope().validateThirdStep();
}
</script>
@stop

@section('content')
<div class="" ng-app="myApp" ng-controller="myCtrl">
    <div class="page-title">
        <div class="col-xs-12">
            <h3>Cadastro de despesa</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row" ng-show="step == 1">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Passo 1 - Informaçoes Gerais</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <h4>Informaçoes Gerais</h4>
                    <div class="form-vertical form-label-left">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                                <div class="form-group">
                                    <label>Nome da despesa*:</label>
                                    <input type="text" ng-model="bill.name" class="form-control has-feedback-left" placeholder="Nome da despesa">
                                    <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <label class="control-label">Grupo*:</label>
                                <select ng-model="groupSelected" ng-change="onGroupSelected()" ng-options="group.name for group in myGroups" class="form-control">
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Data:</label>
                                    <input type="date" ng-model="bill.date" class='form-control' />
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Data de Alerta:</label>
                                    <input type="date" ng-model="bill.alertDate" class='form-control' />
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <h4>Integrantes da Despesa</h4>
                    <div class="row">
                        <div class="form-vertical form-label-left">
                            <div class="col-md-12">
                                <label>Usuario</label>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" ng-model="username"/>
                                    </div>
                                    <div class="col-md-2 col-sm-6 col-xs-12">
                                        <button type="button" class="btn btn-primary btn-block" ng-click="searchUser()">Adicionar</button>
                                    </div>
                                    <% loadMsg %>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 col-xs-4" ng-repeat="member in bill.members">
                            <div class="checkbox">
                                <label><input type="checkbox"  ng-model="member.billParticipant"/> <%member.user.name%> (<%member.user.username%>)</label>
                            </div>
                        </div>    
                    </div>


                    <h5>Informaçao adicional</h5>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <textarea id="message"class="form-control" ng-model="bill.description"></textarea>
                        </div>
                    </div>
                    <div class='ln_solid'></div>
                    <div class='row'>
                        <div class="col-md-3 col-md-offset-9 col-xs-6 col-xs-offset-6">
                            <button class='btn btn-success btn-block' ng-click="validateFirstStep()">Proximo Passo</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row" ng-show="step == 2">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Passo 2 - Cadastro de Itens</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-vertical form-label-left">
                        <div class="row">
                            <div class="col-md-6 col-sm-4 col-xs-8 form-group has-feedback">
                                <div class="form-group">
                                    <label>Nome:</label>
                                    <input type="text" ng-model="rItem.name" class="form-control has-feedback-left" placeholder="Nome da despesa">
                                    <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-3 col-xs-4">
                                <div class="form-group">
                                    <label>Quantidade:</label>
                                    <input type="number" ng-model="rItem.qt" class='form-control' step='1' />
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>Preço unitario:</label>
                                    <div class='row'>
                                        <div class='col-md-6 col-xs-8'>
                                            <input type="number" ng-model="rItem.price" class='form-control' step='0.01' />
                                        </div>
                                        <div class='col-md-6 col-xs-4'>
                                            <button class='btn btn-primary btn-block' ng-click="addItem()">Adicionar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4>Integrantes do Item</h4>
                        <div class="row">
                            <div class="col-md-2 col-xs-4" ng-repeat="member in bill.members| filter: {billParticipant:true}">
                                <div class="checkbox">
                                    <label><input type="checkbox"  ng-model="member.itemParticipant"/> <%member.user.name%> (<%member.user.username%>)</label>
                                </div>
                            </div>    
                        </div>
                        <div class="row">
                            <table class='table table-striped col-md-12 col-xs-12 col-sm-12'>
                                <thead>
                                <th>Item</th>
                                <th>Preço Unitario x Quantidade</th>
                                <th>Distribuiçao</th>
                                <th>Remover</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="item in bill.items">
                                        <td class='col-md-5'>
                                            <input type="text" ng-model='item.name' class="form-control" />
                                        </td>
                                        <td class='col-md-3'>
                                            <div class='col-md-5'>
                                                <input type="number" ng-model="item.price" step = 0.01  class="form-control"/>
                                            </div>
                                            <div class='col-md-2' style="text-align: center;">
                                                X
                                            </div>
                                            <div class='col-md-5'>
                                                <input type="number" ng-model="item.qt" class="form-control"/>
                                            </div>    
                                        </td>
                                        <td class='col-md-2'>
                                            <div ng-if="!checkDistribution(item)">
                                                A distribuiçao esta incorreta
                                            </div>
                                            <div clas='col-md-12' ng-repeat="member in item.members">
                                                <label ><%member.user.name%> (<%member.user.username%>): </label>
                                                <input type="number" ng-model="member.distribution" step="0.01" class="form-control" />
                                            </div>
                                        </td>
                                        <td class='col-md-1'>
                                            <button type="button" class="btn btn-danger btn-sm btn-block" ng-click="removeElement(item, bill.items)">remover</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class='ln_solid'></div>
                        <div class='row'>
                            <div class="col-md-3 col-md-offset-6 col-xs-6">
                                <button class='btn btn-default btn-block' ng-click="setStep(1)">Voltar</button>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <button class='btn btn-success btn-block' ng-click="validateSecondStep()">Proximo Passo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row" ng-show="step == 3">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Passo 3 - Contribuintes</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-vertical form-label-left">
                        <b>Valor total: </b> R$ <% totalItems %>
                        <div class="row">
                            <div class="col-md-3 col-xs-12">
                                <label>Integrante</label>
                                <select ng-model="rContributor.member" class="form-control">
                                    <option ng-repeat="member in bill.members| filter: {billParticipant: true}" ng-value="member"><%member.user.name%> (<%member.user.username%>)</option>
                                </select>
                            </div>
                            <div class="col-md-8 col-xs-12">
                                <label>Valor</label>
                                <div class="row">
                                    <div class="col-md-3 col-xs-6">
                                        <input type="number" ng-model="rContributor.value" min="0" step="0.01" class="form-control" />
                                    </div>
                                    <div class="col-md-3 col-xs-6">
                                        <button type="button" class="btn btn-primary btn-block"ng-click="addContributor()">Adicionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class='table table-striped'>
                                <thead>
                                <th>Contribuidor</th>
                                <th>Valor</th>
                                <th>Remover</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="member in bill.members" ng-if="member.contributor">
                                        <td>
                                            <%member.user.name%> (<%member.user.username%>) 
                                        </td>
                                        <td> 
                                            <input type="number" class="form-control" ng-model="member.contribution" />                                            
                                        </td>
                                        <td>
                                            <button type="button" ng-click="removeContributor(member)" class="btn btn-danger btn-sm">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class='ln_solid'></div>
                    <div class='row'>
                        <div class="col-md-3 col-md-offset-6">
                            <button class='btn btn-default btn-block' ng-click="setStep(2)">Voltar</button>
                        </div>
                        <div class="col-md-3">
                            <form action="{{action("BillController@create")}}" id="billForm" method="post" onsubmit="return validateBillForm();" >
                                {{csrf_field()}}
                                <input type="hidden" value="<% bill %>" name="billJson" />
                                <button type="submit" class='btn btn-success btn-block'>Cadastrar Despesa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop