@extends('shared.layout')

@section('title') Recebimentos @stop

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Recebimentos</h3>
    </div>
    <div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
            <a href="{{action("PaymentController@create")}}" class="btn btn-success btn-block">Registrar Recebimento</a>
        </div>
    </div>
</div>
<div class="x_panel">
    <div class="x_title">
        <h2>Pesquisa</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form class="form-vertical form-label-left" action="{{action("PaymentController@index")}}" method="get">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12 form-group has-feedback">
                    <div class="form-group">
                        <label>Nome do Usuário</label>
                        <input type="text" class="form-control has-feedback-left" id="username" name="username" placeholder="Nome do usuário">
                        <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                    </div>
                </div>

                <div class="col-md-8 col-sm-6 col-xs-12">
                    <label>A partir de</label>
                    <div class="row">
                        <div class="col-md-5 col-sm-6 col-xs-12">
                            <input type="date" class="form-control" name="date">
                        </div>
                        <div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-6">
                            <button type="submit" class="btn btn-success btn-block">Buscar</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@if(isset($payments))
@if(count($payments) > 0)
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Resultado da busca</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Credor</th>
                            <th>Devedor</th>
                            <th>Data</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        @foreach($payments as $payment)
                        <tr>
                            <td>
                                {{$count}}
                                @if($payment->status == "canceled")
                                    <span class="badge bg-red">cancelado</span>
                                @endif
                            </td>
                            <td>{{$payment->receiverUser->toString()}}</td>
                            <td>{{$payment->payerUser->toString()}}</td>
                            <td>
                                {{Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')}}
                            </td>
                            <td>
                                <a href="{{action("PaymentController@show", $payment->id)}}" class="btn btn-primary btn-xs">detalhes</a>
                            </td>
                        </tr>
                        <?php $count++ ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Nenhum recebimento encontrado</h2>
                <div class="clearfix"></div>
            </div>
        </div> 
    </div>
</div>    
@endif
@endif
@stop