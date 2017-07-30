<?php

use Illuminate\Support\Facades\Auth;

$userId = Auth::user()->id;
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>

        Valor a Receber: {{$pending->values->valueToReceive}} <br/>
        Sugestao: <br/>
        <ul>
            @foreach(App\Bill::makeSuggestionToReceiver($pending->bills, $userId) as $name => $suggestion)
            <li>
                Receber de {{$name}}: R$ {{$suggestion}}
            </li>
            @endforeach

        </ul>
        Valor a Pagar: {{$pending->values->valueToPay}} <br/>
        Sugestao: <br/>
        <ul>

            @foreach(App\Bill::makeSuggestionToPay($pending->bills, $userId) as $name => $suggestion)
            <li>
                Receber de {{$name}}: R$ {{$suggestion}}
            </li>
            @endforeach

        </ul>
        <table border='1'>
            <thead>
            <th>Despesa</th>
            <th>Data</th>
            <th>Valor</th>
            <th>Grupo</th>
        </thead>
        <tbody>
            @foreach($pending->bills as $bill)
            <tr>
                <td><a href='{{action("BillController@show", $bill->id)}}'>{{$bill->name}}</a></td>
                <td>{{Carbon\Carbon::parse($bill->date)->format('d/m/Y')}}</td>
                <td>{{$bill->getPendingValue($userId)}}</td>
                <td>{{$bill->group->name}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
