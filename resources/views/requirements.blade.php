<?php

use App\User;

$user = Illuminate\Support\Facades\Auth::user();
$generalInformation = User::getGeneralInformation($user);
?>
@extends('shared.layout')
@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Requerimentos</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <form class="form-vertical form-label-left input_mask">

            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-4 form-group has-feedback">
                    <div class="form-group">
                        <label>Nome do Usuário</label>
                        <input type="text" class="form-control has-feedback-left" id="username" name="username" placeholder="Nome do usuário">
                        <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                    </div>
                </div>

                <div class="col-md-3 col-sm-3 col-xs-3">
                    <div class="form-group">
                        <label>Enviado/Recebido</label>
                        <select class="form-control" name="sentOrReceived">
                            <option>Enviados e Recebidos</option>
                            <option value="sent">Enviados</option>
                            <option value="received">Recebidos</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2 col-sm-2 col-xs-2">
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

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-3">A partir de</label>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                        <input type="date" class="form-control" name="date">
                    </div>
                </div>
            </div>
            <div class="row">            
                <div class="col-md-2 col-md-offset-8">
                    <button type="submit" class="btn btn-default btn-block">Limpar</button>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-block">Buscar</button>
                </div>
            </div>
        </form>

    </div>
</div>
<div class="x_panel">
    <div class="x_content">
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
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop