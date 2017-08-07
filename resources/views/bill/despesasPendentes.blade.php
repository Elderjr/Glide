@extends ('shared.layout')                

@section('content')
<div class="">
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Valor A Receber <small><a href='{{action("PaymentController@create")}}'>(registrar pagamento)</a></small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    R$ {{$pageInfo->pendingValues->valueToReceiver}}
                    <div class="divider"></div>
                    <div>
                        <a href="#" data-toggle="collapse" data-target="#sugestaoReceber">Ver sugestao</a>
                        <div id='sugestaoReceber' class='collapse'>
                            <ul>
                                @foreach(App\Bill::makeSuggestionToReceiver($pageInfo->billsInDebt, $pageInfo->user->id) as $name => $suggestion)
                                <li>Receber de {{$name}} R$ {{$suggestion}}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Valor A Pagar <small> <a href='{{action("RequerimentController@create")}}'>(registrar requerimento)</a></small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    R$ {{$pageInfo->pendingValues->valueToPay}}
                    <div class="divider"></div>
                    <div>
                        <a href="#" data-toggle="collapse" data-target="#sugestaoPagar">Ver sugestao</a>
                        <div id='sugestaoPagar' class='collapse'>
                            <ul>
                                @foreach(App\Bill::makeSuggestionToPay($pageInfo->billsInDebt, $pageInfo->user->id) as $name => $suggestion)
                                <li>Pagar para {{$name}} R$ {{$suggestion}}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Despesas Pendentes</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class='table table-striped'>
                        <thead>
                        <th>Nome</th>
                        <th>Data de Registro</th>
                        <th>Valor a Pagar / Receber</th>
                        <th>Detalhes</th>
                        </thead>
                        <tbody>
                            @foreach($pageInfo->billsInDebt as $bill)
                            <tr>
                                <td>
                                    {{$bill->name}}
                                    @if($bill->isInAlert())
                                        <span class="badge bg-red"> Em Alerta</span>
                                    @endif
                                </td>
                                <td>
                                    {{Carbon\Carbon::parse($bill->created_at)->format('d/m/Y')}}
                                </td>
                                <td>
                                    <?php $member = $bill->getMemberById($pageInfo->user->id);?>
                                    @if($member->needToReceiver())
                                        Receber R$ {{$member->valueToReceiver()}}
                                    @else
                                        Pagar R$ {{$member->valueToPay()}}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{action("BillController@show", $bill->id)}}" class="btn btn-primary btn-xs">detalhes</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

