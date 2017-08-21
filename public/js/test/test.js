var app = angular.module('angularApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});

app.controller('MainCtrl', function($scope, name, id) {
    $scope.id = id;
    $scope.name = name;
});