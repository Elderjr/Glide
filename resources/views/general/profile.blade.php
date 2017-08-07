@extends('shared.layout')
@section('content')

<div class="row">
    <div class="col-xs-12">
        <div class="page-title">
            <h3>Meu Perfil</h3>
        </div>  
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Informaçoes Gerais</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form class="form-vertical form-label-left" action="{{action("GeneralController@updateProfile")}}" method="post">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label>Nome:</label>
                            <input type="text" id="name" name="name" class="form-control " value="{{$generalInformation->user->name}}">
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">E-mail</label>
                            <input type="text" id="email" name="email" class="form-control" value="{{$generalInformation->user->email}}">
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="row">
                        <div class="col-md-5 col-md-offset-7 col-xs-6 col-xs-offset-6">
                            <button type="submit" class="btn btn-success btn-block">Confirmar</button>
                        </div>
                    </div>
                </form>   
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Alterar Senha</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">                
                <form class="form-vertical form-label-left" action="{{action("GeneralController@updatePassword")}}" method="post">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">Senha Atual:</label>
                            <input type="password" id="oldPassword" name="oldPassword" class="form-control">
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">Nova Senha</label>
                            <input type="password" id="newPassword1" name="newPassword" class="form-control" />
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">Confirmação</label>
                            <input type="password" id="newPassword2" name="confirmPassword" name="email" class="form-control " />
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="row">
                        <div class="col-md-5 col-md-offset-7 col-xs-6 col-xs-offset-6">
                            <button type="submit" class="btn btn-success btn-block">Alterar Senha</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
