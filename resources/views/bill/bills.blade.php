<?php $userId = Illuminate\Support\Facades\Auth::user()->id; ?>
@extends('shared.layout')

@section('title') Minhas Despesas @stop

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Minhas Despesas</h3>
    </div>
    <div class="title_right">
        <div class="col-md-6 col-sm-5 col-xs-12 form-group pull-right top_search">
            <a href="{{action("BillController@create")}}" class="btn btn-success btn-block">Registrar Nova Despesa</a>
        </div>
    </div>
</div>
<div class="x_panel">
    <div class="x_title">
        <h2>Pesquisa</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form class="form-vertical form-label-left" method="{{action("BillController@index")}}">
            <div class="row">
                <div class="col-md-4 col-sm-8 col-xs-12 form-group has-feedback">
                    <div class="form-group">
                        <label>Nome da Despesa</label>
                        <input type="text" class="form-control has-feedback-left" id="billName" name="billName" placeholder="Nome da despesa">
                        <span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>
                    </div>
                </div>

                <div class="col-md-2 col-sm-4 col-xs-6">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" name="billStatus">
                            <option>Todas</option>
                            <option value="pending">Pendente</option>
                            <option value="inAlert">Em alerta</option>
                            <option value="finished">Concluída</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-8 col-xs-6">
                    <div class="form-group">
                        <label>Grupo</label>
                        <select class="form-control" name="billGroupId">
                            <option>Todos</option>
                            @foreach($myGroups as $group)
                            <option>{{$group->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label class="control-label">A partir de</label>
                        <input type="date" class="form-control" name="billDate">

                    </div>
                </div>
            </div>
            <div class="row">            
                <div class="col-md-2 col-md-offset-10 col-sm-3 col-sm-offset-9 col-xs-6 col-xs-offset-6">
                    <button type="submit" class="btn btn-success btn-block">Buscar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@if(isset($bills))
@if(count($bills) > 0)
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
                            <th>Nome</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        @foreach($bills as $bill)
                        <tr>
                            <td>{{$count}}</td>
                            <td>
                                {{$bill->name}}      
                                @if($bill->isInAlert() && !$bill->getMemberById($userId)->isSettled())
                                <span class="badge bg-red">Em Alerta</span>
                                @endif
                            </td>
                            <td>{{$bill->total}}</td>
                            <td>
                                {{Carbon\Carbon::parse($bill->created_at)->format('d/m/Y')}}
                            </td>
                            <td>
                                <a href="{{action("BillController@show", $bill->id)}}" class="btn btn-primary btn-xs">detalhes</a>
                            </td>
                        </tr>
                        <?php $count++ ?>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="btn-group">
                        <a href="{{$bills->url(1)}}" class="btn btn-default">Inicio</a>
                        @if($bills->currentPage() - 1 >= 1)
                        <a  href="{{$bills->previousPageUrl()}}" class="btn btn-default"> {{$bills->currentPage() - 1}} </a>
                        @endif
                        <a href="{{$bills->url($bills->currentPage())}}" class="btn btn-success" >{{$bills->currentPage()}}</a>
                        @if($bills->currentPage() + 1 <= $bills->lastPage())
                        <a href="{{$bills->nextPageUrl()}}" class="btn btn-default">{{$bills->currentPage() + 1}}</a>
                        @endif
                        <a href="{{$bills->url($bills->lastPage())}}" class="btn btn-default" type="button">Fim</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Nenhuma Despesa encontrada</h2>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
@endif
@endif
@stop