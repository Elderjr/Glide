@extends ('shared.layout')                

@section('content')
<div class="">
    <div class="row">
        <div class="x_content">
            <div class='col-md-3 col-sm-2'>
                <a class="btn btn-app" style="display: block;">
                    <span class="badge bg-orange">{{count($pageInfo->billsInDebt)}}</span>
                    <i class="fa fa-edit"></i> Despesas Pendentes
                </a>
            </div>
            <div class='col-md-3 col-sm-2'>
                <a class="btn btn-app" style="display: block;">
                    <i class="fa fa-edit"></i> Cadastrar Despesa
                </a>
            </div>
            <div class='col-md-3 col-sm-2'>
                <a class="btn btn-app" style="display: block;">
                    <i class="fa fa-edit"></i> Minhas Despesas
                </a>
            </div>
            <div class='col-md-3 col-sm-2'>
                <a class="btn btn-app" style="display: block;">
                    <i class="fa fa-edit"></i> Historico
                </a>
            </div>
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
        <div class="clearfix"></div>
    </div>
    @if(count($pageInfo->user->receiveRequeriments) > 0)
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Requerimentos recebidos</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <?php $count = 0; ?>
                    @foreach($pageInfo->user->receiveRequeriments as $requeriment)
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
                                        <button class='btn btn-danger btn-sm'>Cancelar</button>
                                        <button class="btn btn-success btn-sm">Confirmar</button>
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

