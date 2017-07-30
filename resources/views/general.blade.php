
<html>
    <head>
        <script src="{{URL::asset('js/angular.min.js')}}"></script>
        <script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});
app.controller('generalInformationController', ['$scope', function ($scope) {
    $scope.generalInformation = JSON.parse('{!!$generalInformation!!}');
}]);
app.controller('generalController', ['$scope', function ($scope) {
    $scope.pendingValues = JSON.parse('{!!$pendingValues!!}');
}]);
        </script>
    </head>

    <body>
        GeneralInformation: {{$generalInformation}}<br/><br/>
        PendingValues: {{$pendingValues}}
        <hr>
        <div ng-app="myApp" >
            <div ng-controller="generalInformationController">
                Menu:
                <ul>
                    <li><a href="">Historico</a></li>
                    <li><a href="{{action("BillController@create")}}">Cadastrar Despesa</a></li>
                    <li><a href="{{action("GroupController@create")}}">Cadastrar Grupo</a></li>
                    <li><a href="{{action("BillController@pendingBills")}}">Despesas Pendentes</a></li>
                    <li><a href="{{action("GroupController@index")}}">Gerenciador de Grupos</a></li>
                    <li><a href="{{action("PaymentController@create")}}">Registrar Pagamento</a></li>
                    <li><a href="">Registrar Requerimento</a></li>
                </ul>
                Meus Grupos: @{generalInformation.user.myGroups.length}
                <ul>
                    <li ng-repeat="group in generalInformation.user.myGroups">
                        @{group.name}
                    </li>
                </ul>
                Despesas em Alerta: @{generalInformation.alertBills.length}
                <ul>
                    <li ng-repeat="bill in generalInformationl.alertBills">
                        @{bill.name}
                    </li>
                </ul>
            </div>
            <hr>
            <div ng-controller="generalController">
                Valor a Receber: R$ @{pendingValues.valueToReceive}<br/>
                Valor a Pagar: R$ @{pendingValues.valueToPay}<br/>
                Total despesas pendentes: @{pendingValues.totalPendingBills}<br/>
            </div>
            
        </div>
    </body>
</html>