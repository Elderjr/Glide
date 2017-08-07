@extends ('shared.layout')                

@section('content')

<div class="row">
    <div class='col-md-3 col-sm-3 col-xs-6'>
        <a href="{{action("BillController@pendingBills")}}" class="btn btn-app" style="display: block; margin-left: 0px;">
            <span class="badge bg-orange">{{count($pageInfo->billsInDebt)}}</span>
            <i class="fa fa-edit"></i> Despesas Pendentes
        </a>
    </div>
    <div class='col-md-3 col-sm-3 col-xs-6'>
        <a href="{{action("BillController@create")}}" class="btn btn-app" style="display: block; margin-left: 0px;">
            <i class="fa fa-edit"></i> Cadastrar Despesa
        </a>
    </div>


    <div class='col-md-3 col-sm-3 col-xs-6'>
        <a href="{{action("BillController@index")}}" class="btn btn-app" style="display: block; margin-left: 0px;">
            <i class="fa fa-edit"></i> Minhas Despesas
        </a>
    </div>
    <div class='col-md-3 col-sm-3 col-xs-6'>
        <a href="{{action("GroupController@index")}}" class="btn btn-app" style="display: block; margin-left: 0px;">
            <i class="fa fa-edit"></i> Gerenciador de Grupos
        </a>
    </div>
</div>
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

</div>
@if(count($pageInfo->waitingRequirements) > 0)
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Requerimentos recebidos</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $count = 0; ?>
                @foreach($pageInfo->waitingRequirements as $requeriment)
                @if($count % 3 == 0)
                <div class="row">
                    @endif
                    <div class="col-md-4 col-sm-4 col-xs-10">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Requerimento de {{$requeriment->sourceUser->name}}</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                {{$requeriment->sourceUser->toString()}} requeriu o pagamento de R$ {{$requeriment->value}} em 
                                {{ Carbon\Carbon::parse($requeriment->created_at)->format('d/m/Y') }}.
                                <br/>
                                Descriçao: {{$requeriment->description}}
                                <div class="divider"></div>
                                <div style="text-align: right;">
                                    <a href="{{action("RequerimentController@reject", $requeriment->id)}}" class='btn btn-danger btn-sm'>Cancelar</a>
                                    <a href="{{action("RequerimentController@showAccept", $requeriment->id)}}" class="btn btn-success btn-sm">Confirmar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($count % 3 == 0)
                </div>
                @endif
                <?php $count++; ?>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
</div>
<!-- /page content -->
@stop

