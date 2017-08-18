@extends('shared.layout')

@section('title') Cadastro de Recebimento @stop

@section('jsImport')
@if (isset($payment))
<script src="{{URL::asset('js/angular.min.js')}}"></script>
<script src="{{URL::asset('js/decimal.min.js')}}"></script>
<script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});
app.controller("myCtrl", function ($scope) {
    $scope.pageInfo = JSON.parse('{!!json_encode($payment)!!}');
    $scope.total = function () {
        var sum = 0.0;
        for (var i = 0; i < $scope.pageInfo.paymentBills.length; i++) {
            if ($scope.isNumber($scope.pageInfo.paymentBills[i].value)) {
                sum = Decimal.add(sum, $scope.pageInfo.paymentBills[i].value).toNumber();
            }
        }
        return sum;
    }

    $scope.makeDistribution = function () {
        var value = $scope.automaticPayment;
        for (var i = 0; i < $scope.pageInfo.paymentBills.length; i++) {
            if (value > $scope.pageInfo.paymentBills[i].bill.debt) {
                $scope.pageInfo.paymentBills[i].value = parseFloat($scope.pageInfo.paymentBills[i].bill.debt);
                value = Decimal.sub(value, $scope.pageInfo.paymentBills[i].bill.debt).toNumber();
            } else if (value > 0) {
                $scope.pageInfo.paymentBills[i].value = value;
                value = 0;
            } else {
                $scope.pageInfo.paymentBills[i].value = 0;
            }
        }
    }

    $scope.isNumber = function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
});
</script>
@endif

@stop

@section('content')
<div class='row'>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <form class="form-horizontal form-label-left">
                <div class="row">
                    <div class="col-md-12">
                        <label>Username do usuario</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" class="form-control has-feedback-left" id="username" name="username" placeholder="Username do usuário">
                                    <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-block" value="Buscar" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @if(isset($payment))
            <div ng-app="myApp" ng-controller="myCtrl">
                <hr/>
                <div ng-if="pageInfo.paymentBills.length == 0">
                    <h4>Não existem valores para receber de @{pageInfo.payerUser.name}</h4>
                </div>
                <div ng-show="pageInfo.paymentBills.length > 0">
                    <h4>Dívidas com @{pageInfo.payerUser.name}</h4>
                    <form action="{{action('PaymentController@store')}}" method="post">
                        {{ csrf_field()}}
                        <input type="hidden" name="paymentJson" value="@{pageInfo}" />
                        <div class="row">
                            <div class="col-md-8">
                                <label>Distribuir recebimento automaticamente:</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="number" class="form-control" step="0.01" ng-model="automaticPayment">
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
                                <th>Recebimento</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="paymentBill in pageInfo.paymentBills">
                                        <td>@{pageInfo.paymentBills.indexOf(paymentBill) + 1}</td>
                                        <td>
                                            @{paymentBill.bill.name}
                                            <span class="badge bg-red" ng-if="paymentBill.bill.isInAlert">Em Alerta</span>
                                        </td>
                                        <td>@{paymentBill.bill.debt}</td>
                                        <td>
                                            <div ng-if="paymentBill.value > paymentBill.bill.debt">
                                                Atenção, o recebimento está maior que a dívida
                                            </div>
                                            <div ng-if="!isNumber(paymentBill.value)">
                                                Pagamento esta na forma incorreta
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-6">
                                                <input type="number" step="0.01" ng-model="paymentBill.value" min="0" class="form-control" />
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
                                <input type="submit" value="Registrar Recebimento" class="btn btn-success btn-block" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@stop