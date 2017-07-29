
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
                $scope.payments = JSON.parse('{!!$paymentsJson!!}');
            });
        </script>
    </head>

    <body>

        <div ng-app="myApp" ng-controller="myCtrl">
            Pagamento de <%payments.payerUser.name%> para <%payments.receiverUser.name%>
            <br/>
            JSON: <%payments%>
            <form action="{{action('PaymentController@store')}}" method="post">
                {{ csrf_field()}}
                <input type="hidden" name="paymentsJson" value="<%payments%>" />
                <table border="1">
                    <thead>
                        <th>Despesa</th>
                        <th>Divida</th>
                        <th>Pagamento</th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="bill in payments.bills">
                            <td><%bill.name%></td>
                            <td><%bill.debt%></td>
                            <td>
                                <div ng-if="bill.payment > bill.debt">
                                    Atençao, o pagemento esta maior que a divida
                                </div>
                                <input type="number" step="0.01" ng-model="bill.payment" min="0"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="submit" value="registrar pagamento" />
            </form>
        </div>

    </body>
</html>