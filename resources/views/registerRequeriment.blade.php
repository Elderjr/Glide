<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="{{URL::asset('js/angular.min.js')}}">
        </script>
        <script>
            @if(isset($requerimentJson))
                var app = angular.module('myApp', [], function ($interpolateProvider) {
                    $interpolateProvider.startSymbol('@{');
                    $interpolateProvider.endSymbol('}');
                });
                app.controller("myCtrl", function ($scope) {
                    $scope.requeriment = JSON.parse('{!!$requerimentJson!!}');
                });
            @endif
        </script>
    </head>
    <body>
        @if(session('feedback'))
            @if(session('feedback')->success != null)
                {{session('feedback')-> success}}
            @endif
            @if(session('feedback')->alert != null)
                {{session('feedback')-> alert}}
            @endif
            @if(session('feedback')->error != null)
                {{session('feedback')-> error}}
            @endif
        @endif
        
        <form action="{{action("RequerimentController@create")}}" method="get">
            Usuario: <input type="text" name="username" />
            <input type="submit" value="buscar" />
        </form>
        
        @if(isset($requerimentJson))
        <div ng-app="myApp" ng-controller="myCtrl">
            <br/>
            Voce esta em debito com as seguintes contas com @{requeriment.user.name} (@{requeriment.user.username})
            <ul>
                <li ng-repeat="bill in requeriment.bills">
                    @{bill.name} (R$ @{bill.debt})
                </li>
            </ul>
            Total: R$: @{requeriment.total}
            <form action="{{action('RequerimentController@store')}}" method="post">
                {{ csrf_field()}}
                Enviar requerimento de: <input type="number" name="requerimentValue" step="0.01" />
                <input type="hidden" value="@{requeriment.user.id}" name="requerimentUserId"/>
                <br/>
                Descricao:
                <textarea name="requerimentDescription">
                    
                </textarea>
                <input type="submit" value="enviar requerimento" />
            </form>
        </div>
        @endif
    </body>
</html>
