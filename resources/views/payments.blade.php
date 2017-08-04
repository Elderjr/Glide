@extends('shared.layout')
@section('content')
{{dump($payments)}}
<div class="page-title">
    <div class="title_left">
        <h3>Pagamentos</h3>
    </div>
    <div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
            <a href="" class="btn btn-success btn-block">Registrar Novo Pagamento</a>
        </div>
    </div>
</div>
<div class="x_panel">
    <div class="x_title">
        <h2>Pesquisa</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form class="form-vertical form-label-left" action="{{action("PaymentController@index")}}"m ethod="get">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-4 form-group has-feedback">
                    <div class="form-group">
                        <label>Nome do Usuário</label>
                        <input type="text" class="form-control has-feedback-left" id="username" name="username" placeholder="Nome do usuário">
                        <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="col-md-8">
                    <label>A partir de</label>
                    <div class="row">
                        <div class="col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <input type="date" class="form-control" name="date">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-default btn-block">Limpar</button>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-block">Buscar</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@if(isset($payments))
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
                            <td>{{$count}}</td>
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
@endif
@stop