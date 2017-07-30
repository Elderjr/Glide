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

app.controller('groupDetailsController', ['$scope', function ($scope) {

    }]);
        </script>
    </head>

    <body>
        <div ng-app="myApp" >
            @if(session('feedback'))
                @if(session('feedback')->success != null)
                    {{session('feedback')->success}}
                @endif
                @if(session('feedback')->alert != null)
                    {{session('feedback')->alert}}
                @endif
                @if(session('feedback')->error != null)
                    {{session('feedback')->error}}
                @endif
            @endif
            <div ng-controller="groupDetailsController">
                {{$group-> name}}
                <br/>
                <table>
                    <tr>
                        <td>
                            Membro
                        </td>
                        @if($group->getMemberById($userId)->admin)
                        <td>
                            Tornar Administrador
                        </td>
                        <td>
                            Remover
                        </td>
                        @endif
                    </tr>
                    @foreach($group->members as $member)
                    <tr>
                        <td>
                            {{$member->user->name}}
                            @if($member->admin)
                            <span>
                                (administrador)
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($group->getMemberById($userId)->admin)
                            <form action="{{action('GroupController@setAdminAsTrue', $group->id)}}" method="post">
                                {{ csrf_field()}}
                                <input type="hidden" name="userId" value="{{$member->user->id}}" />
                                <input type="submit" value="Tornar Admin">
                            </form>
                        </td>
                        <td>
                            <form action="{{action('GroupController@removeMember', $group->id)}}" method="post">
                                {{ csrf_field()}}
                                <input type="hidden" name="userId" value="{{$member->user->id}}" />
                                <input type="submit" value="Remover">
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
                <br/>
                @if($group->getMemberById($userId)->admin)
                <form action="{{action('GroupController@storeMember', $group->id)}}" method="post">
                    {{ csrf_field()}}
                    <input type="text" name="username"/>
                    <button>Adicionar</button>
                </form>
                @endif
            </div>
        </div>
    </body>
</html>