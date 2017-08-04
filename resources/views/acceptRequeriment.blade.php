@extends('shared.layout')

@section('jsImport')
<script src="{{URL::asset('js/angular.min.js')}}"></script>
<script src="{{URL::asset('js/decimal.min.js')}}"></script>
<script>
@if (isset($paymentsJson))
        var app = angular.module('myApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('@{');
            $interpolateProvider.endSymbol('}');
        });
app.controller("myCtrl", function ($scope) {
    $scope.pageInfo = JSON.parse('{!!json_encode($pageInfo)!!}');
    $scope.total = function () {
        var sum = 0.0;
        for (var i = 0; i < $scope.pageInfo.bills.length; i++) {
            if ($scope.isNumber($scope.pageInfo.bills[i].payment)) {
                sum = Decimal.add(sum, $scope.pageInfo.bills[i].payment).toNumber();
            }
        }
        return sum;
    }
    
    $scope.makeDistribution = function(){
        var value = $scope.automaticPayment;
        for (var i = 0; i < $scope.pageInfo.bills.length; i++) {
            if(value > $scope.pageInfo.bills[i].debt){
                $scope.pageInfo.bills[i].payment = $scope.pageInfo.bills[i].debt;
                value = Decimal.sub(value, $scope.pageInfo.bills[i].debt).toNumber();
            }else if(value > 0){
                $scope.pageInfo.bills[i].payment = value;
                value = 0;
            }else{
                $scope.pageInfo.bills[i].payment = 0;
            }
        }
    }
    
    $scope.isNumber = function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
});
        @endif
</script>
@stop

@section('content')
<div class='row'>
    <div class="col-md-12 col-sm-12 col-xs-12" ng-app="myApp" ng-controller="myCtrl">
        <div class="x_panel">
            {!!json_encode($pageInfo)!!}
            @if(count($pageInfo->bills) == 0)
            <h4>Não existem dívidas com @{pageInfo.requirement.sourceUser.name}</h4>
            @else
            <h4>Dívidas com @{pageInfo.payerUser.name}</h4>
            <form action="{{action('PaymentController@store')}}" method="post">
                <div class="row">
                    <div class="col-md-8">
                        <label>Distribuir pagamento automaticamente:</label>
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
                {{ csrf_field()}}
                <input type="hidden" name="paymentsJson" value="@{pageInfo}" />
                <div class="x_content">
                    <table class="table table-striped" width='30%'>
                        <thead>
                        <th>#</th>
                        <th>Despesa</th>
                        <th>Dívida</th>
                        <th>Pagamento</th>
                        </thead>
                        <tbody>
                            <tr ng-repeat="bill in pageInfo.bills">
                                <td>@{payments.bills.indexOf(bill) + 1}</td>
                                <td>@{bill.name}</td>
                                <td>@{bill.debt}</td>
                                <td>
                                    <div ng-if="bill.payment > bill.debt">
                                        Atenção, o pagamento está maior que a dívida
                                    </div>
                                    <div ng-if="!isNumber(bill.payment)">

                                    </div>
                                    <div class="col-md-3 col-sm-3 col-xs-6">
                                        <input type="number" step="0.01" ng-model="bill.payment" min="0" class="form-control">
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
                        <input type="submit" value="Registrar Pagamento" class="btn btn-success btn-block" />
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@stop