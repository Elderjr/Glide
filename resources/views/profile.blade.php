@extends('shared.layout')
@section('content')

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
        <div class="x_panel">
            <div class="x_title">
                <h2>Perfil</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br />
                <form class="form-horizontal form-label-left" action="{{action("GeneralController@profile")}}" method="get">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Nome</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="name" name="name" class="form-control col-md-7 col-xs-12" value="{{$generalInformation->user->name}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">E-mail</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="email" name="email" class="form-control col-md-7 col-xs-12" value="{{$generalInformation->user->email}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button type="submit" class="btn btn-success">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">
        <div class="x_panel">
            <div class="x_title">
                <h2>Alterar Senha</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br />
                <form class="form-horizontal form-label-left">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Senha Atual</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="password" id="oldPassword" name="oldPassword" class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Nova Senha</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="password" id="newPassword1" name="newPassword1" class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Confirmação</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="password" id="newPassword2" name="newPassword2" name="email" class="form-control col-md-7 col-xs-12">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button type="submit" class="btn btn-success">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
