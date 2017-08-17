@extends('shared.layout')

@section('title') Confirmaçao de Requerimento @stop

@section('cssImport')
<link href="{{URL::asset('css/pnotify.custom.min.css')}}" rel="stylesheet">
@stop

@section('jsImport')
<script src="{{URL::asset('js/angular.min.js')}}"></script>
<script src="{{URL::asset('js/decimal.min.js')}}"></script>
<script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});
app.controller("myCtrl", function ($scope) {
    $scope.pageInfo = JSON.parse('{!!json_encode($pageInfo)!!}');
    $scope.total = function () {
        var sum = 0.0;
        for (var i = 0; i < $scope.pageInfo.paymentBills.length; i++) {
            if ($scope.isNumber($scope.pageInfo.paymentBills[i].value)) {
                sum = Decimal.add(sum, $scope.pageInfo.paymentBills[i].value).toNumber();
            }
        }
        return sum;
    }

    $scope.makeDistribution = function(){
        var value = $scope.pageInfo.requirement.value;
        for (var i = 0; i < $scope.pageInfo.paymentBills.length; i++) {
            if (value > $scope.pageInfo.paymentBills[i].bill.debt){
                $scope.pageInfo.paymentBills[i].value = $scope.pageInfo.paymentBills[i].bill.debt;
                value = Decimal.sub(value, $scope.pageInfo.paymentBills[i].bill.debt).toNumber();
            } else if (value > 0){
                $scope.pageInfo.paymentBills[i].value = value;
                value = 0;
            } else{
                $scope.pageInfo.paymentBills[i].value = 0;
            }
        }
    }

    $scope.validateRequirement = function(){
        if ($scope.total() != $scope.pageInfo.requirement.value){
            new PNotify({
                title: 'Notificaçao de erro',
                text: "O total do pagamento deve ser igual ao valor do requerimento",
                type: 'error'
            });
        return false;
        }
        return true;
    }

    $scope.isNumber = function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
});

function validateRequirementForm() {
    return angular.element($("#requirementForm")).scope().validateRequirement();
}
</script>
@stop

@section('content')
<div class='row'>
    <div class="col-md-12 col-sm-12 col-xs-12" ng-app="myApp" ng-controller="myCtrl">
        <div class="x_panel">
            <div ng-if="pageInfo.paymentBills.length == 0">
                <h4>Não existem dívidas com @{pageInfo.requirement.sourceUser.name}</h4>
            </div>
            <div ng-show ="pageInfo.paymentBills.length > 0">
                <h4>Dívidas com @{pageInfo.requirement.source_user.name}</h4>
                <form action="{{action('RequerimentController@accept', $pageInfo->requirement->id)}}" method="post" id="requirementForm" onsubmit="return validateRequirementForm();">
                    {{ csrf_field()}}
                    <input type="hidden" name="acceptedRequirementJson" value="@{pageInfo}" />
                    <div class="row">
                        <div class="col-md-8">
                            <label>Distribuir pagamento automaticamente:</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="number" class="form-control" step="0.01" value ="@{pageInfo.requirement.value}" ng-disabled="true">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-block" ng-click="makeDistribution()" >Distribuir</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="x_content">
                        <table class="table table-striped" width='30%'>
                            <thead>
                            <th>#</th>
                            <th>Despesa</th>
                            <th>Dívida</th>
                            <th>Pagamento</th>
                            </thead>
                            <tbody>
                                <tr ng-repeat="payment in pageInfo.paymentBills">
                                    <td>@{pageInfo.paymentBills.indexOf(payment) + 1}</td>
                                    <td>@{payment.bill.name}</td>
                                    <td>@{payment.bill.debt}</td>
                                    <td>
                                        <div ng-if="payment.value > payment.bill.debt">
                                            Atenção, o pagamento está maior que a dívida
                                        </div>
                                        <div ng-if="!isNumber(payment.value)">
                                            Pagamento nao esta no formato correto
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-6">
                                            <input type="number" step="0.01" ng-value="payment.value" ng-model="payment.value" min="0" class="form-control" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <b>Total:</b> R$ @{total()}
                    <h5>Informaçao adicional</h5>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <textarea id="message"class="form-control" ng-model="pageInfo.description" ></textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-9">
                            <input type="submit" value="Aceitar Requerimento" class="btn btn-success btn-block" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop