@extends('shared.layout')

@section('title') Requerimentos @stop

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Meus Requerimentos</h3>
    </div>
    <div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
            <a href="{{action("RequerimentController@create")}}" class="btn btn-success btn-block">Registrar Requerimento</a>
        </div>
    </div>
</div>
<div class="x_panel">
    <div class="x_title">
        <h2>Pesquisa</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form class="form-vertical form-label-left" action="{{action("RequerimentController@index")}}" method="get">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="form-group">
                        <label>Enviado / Recebido</label>
                        <select class="form-control" name="sentOrReceived">
                            <option>Enviados e Recebidos</option>
                            <option value="sent">Enviados</option>
                            <option value="received">Recebidos</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 col-xs-12 form-group has-feedback">
                    <div class="form-group">
                        <label>Enviado para / Recebido de</label>
                        <input type="text" class="form-control has-feedback-left" id="username" name="username" placeholder="Nome do usuário">
                        <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-2 col-xs-6">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" name="status">
                            <option>Todos</option>
                            <option value="accepted">Aceito</option>
                            <option value="rejected">Rejeitado</option>
                            <option value="waiting">Aguardando</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-3 col-sm-offset-0 col-xs-12">
                    <div class="form-group">
                        <label class="control-label">A partir de</label>
                        <input type="date" class="form-control" name="date">
                    </div>
                </div>
            </div>
            <br/>
            <div class="row">            
                <div class="col-md-3 col-md-offset-9 col-sm-3 col-sm-offset-9 col-xs-6 col-xs-offset-6">
                    <button type="submit" class="btn btn-success btn-block">Buscar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@if(isset($requirements))

    @if(count($requirements) > 0)
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
                                    <th>Remetente</th>
                                    <th>Destinatário</th>
                                    <th>Enviado/Recebido</th>
                                    <th>Estado</th>
                                    <th>Data</th>
                                    <th>Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @foreach($requirements as $req)
                                <tr>
                                    <td>{{$count}}</td>
                                    <td>{{$req->sourceUser->toString()}}</td>
                                    <td>{{$req->destinationUser->toString()}}</td>
                                    <td>
                                        @if($req->sourceUser->id == Illuminate\Support\Facades\Auth::user()->id)
                                        Enviado
                                        @else
                                        Recebido
                                        @endif
                                    </td>
                                    <td>
                                        @if($req->status == "accepted")
                                        <span class="badge bg-green">Aceito</span>
                                        @elseif($req->status == "rejected")
                                        <span class="badge bg-red">Rejeitado</span>
                                        @else
                                        <span class="badge bg-orange">Aguardando</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{Carbon\Carbon::parse($req->created_at)->format('d/m/Y')}}
                                    </td>
                                    <td>
                                        <a href="{{action("RequerimentController@show", $req->id)}}" class="btn btn-primary btn-xs">detalhes</a>
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
                        <h2>Nenhum requerimento encontrado</h2>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
@stop