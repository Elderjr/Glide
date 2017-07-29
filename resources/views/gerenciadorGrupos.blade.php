<?php
    $myGroups = json_decode($myGroupsJson);
?>
<html>
    <head>
        <script src="{{URL::asset('js/angular.min.js')}}"></script>
        <script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});

app.controller('groupManager', ['$scope', function ($scope) {
    $scope.myGroups = JSON.parse('{!!$myGroupsJson!!}');
}]);
        </script>
    </head>

    <body>
        Todos os Grupos: {{$myGroupsJson}}<br/><br/>
        <hr>
        <div ng-app="myApp" >
            @if(session('feedback'))
                @if(session('feedback')->success != null)
                    {{session('feedback')->success}}
                @elseif(session('feedback')->alert != null)
                    {{session('feedback')->alert}}
                @elseif(session('feedback')->error != null)
                    {{session('feedback')->error}}
                @endif
            @endif
            <div ng-controller="groupManager">
                <table border="1">
                    <thead>
                        <th>Grupo</th>
                        <th>Sair</th>
                    </thead>
                    <tbody>
                        @foreach($myGroups as $group)
                        <tr>
                            <td>
                                {{$group->name}}
                                @if($group->admin)
                                <span>
                                    (administrador)
                                </span>
                                @endif
                            </td>
                            <td>
                                <form action="{{action("GroupController@leaveGroup", $group->id)}}" method="post">
                                    {{ csrf_field()}}
                                    <button> Sair </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </body>
</html>