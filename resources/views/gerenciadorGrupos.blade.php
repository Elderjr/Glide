<?php
    $userId = Illuminate\Support\Facades\Auth::user()->id;
?>
<html>
    <head>
        <script src="{{URL::asset('js/angular.min.js')}}"></script>
        <script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});
        </script>
    </head>
    <body>
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
                                <a href="{{action("GroupController@show", $group->id)}}">{{$group->name}}</a>
                                @if($group->getMemberById($userId)->admin)
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