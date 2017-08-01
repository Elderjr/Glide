@extends('shared.layout')

@section('content')
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
            @if (isset($paymentsJson))
                    var app = angular.module('myApp', [], function ($interpolateProvider) {
                        $interpolateProvider.startSymbol('@{');
                        $interpolateProvider.endSymbol('}');
                    });
            app.controller("myCtrl", function ($scope, $http) {
                $scope.payments = JSON.parse('{!!json_encode($paymentsJson)!!}');
            });
                    @endif
        </script>
    </head>

    <body>
        <div class="col-md-12 col-sm-12 col-xs-12" ng-app="myApp" ng-controller="myCtrl">
            <div class="x_panel">
                <form action="{{action("PaymentController@create")}}" method="get">
                    <h5>Nome de usuário do pagante:</h5> <input type="text" name="username" />
                    <input type="submit" value="Buscar" class="btn btn-primary btn-sm" />
                </form>
                @if(isset($paymentsJson))
                <hr/>
                @if(count($paymentsJson->bills) == 0)
                <h4>Não existem dívidas de @{payments.payerUser.name}</h4>
                @else
                <br/>
                <form action="{{action('PaymentController@store')}}" method="post">
                    {{ csrf_field()}}
                    <input type="hidden" name="paymentsJson" value="@{payments}" />
                    <div class="x_content" style="text-align: center;">
                        <table class="table table-striped" width='30%'>
                            <thead>
                            <th>#</th>
                            <th>Despesa</th>
                            <th>Dívida</th>
                            <th>Pagamento</th>
                            </thead>
                            <tbody>
                                <tr ng-repeat="bill in payments.bills">
                                    <td>@{payments.bills.indexOf(bill) + 1}</td>
                                    <td>@{bill.name}</td>
                                    <td>@{bill.debt}</td>
                                    <td>
                                        <div ng-if="bill.payment > bill.debt">
                                            Atenção, o pagamento está maior que a dívida
                                        </div>
                                        <input type="number" step="0.01" ng-model="bill.payment" min="0"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <center><input type="submit" value="Registrar Pagamento" class="btn btn-success btn-sm" /></center>
                </form>
                @endif
                @endif
            </div>
        </div>
    </body>
</html>
@stop