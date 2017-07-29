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
        </script>
    </head>

    <body>

        <div ng-app="myApp" ng-controller="generalInformationController">
        </div>
    </body>
</html>