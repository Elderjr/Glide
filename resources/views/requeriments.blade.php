<?php
    $user = Illuminate\Support\Facades\Auth::user();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        Requerimentos recebidos
        <table border="1">
            <thead>
                <th>Origem</th>
                <th>Destino</th>
                <th>Valor</th>
                <th>Descriçao</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach($user->receiveRequeriments as $requeriment)
                <tr>
                    <td>
                        {{$requeriment->sourceUser->toString()}}
                        @if($requeriment->status == "waiting")
                            (<a href="{{action("RequerimentController@showAccept", $requeriment->id)}}">aceitar</a>)
                        @endif
                    </td>
                    <td>{{$requeriment->destinationUser->toString()}}</td>
                    <td>{{$requeriment->value}}</td>
                    <td>{{$requeriment->description}}</td>
                    <td>{{$requeriment->status}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <hr/>
        Requerimentos enviados
        <table border="1">
            <thead>
                <th>Origem</th>
                <th>Destino</th>
                <th>Valor</th>
                <th>Descriçao</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach($user->sendRequeriments as $requeriment)
                <tr>
                    <td>{{$requeriment->sourceUser->toString()}}</td>
                    <td>{{$requeriment->destinationUser->toString()}}</td>
                    <td>{{$requeriment->value}}</td>
                    <td>{{$requeriment->description}}</td>
                    <td>{{$requeriment->status}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
