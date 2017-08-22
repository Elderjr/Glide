@extends('shared.layout')

@section('title') Cadastro de Despesa @stop

@section('cssImport')
<link href="{{URL::asset('css/pnotify.custom.min.css')}}" rel="stylesheet">
@stop

@section('jsImport')
<script src="{{URL::asset('js/angular.min.js')}}"></script>
<script src="{{URL::asset('js/decimal.min.js')}}"></script>
<script src="{{URL::asset('js/bills/registerBillApp.js')}}"></script>
<script src="{{URL::asset('js/shared/ngEnterDirective.js')}}"></script>
<script>
    app.value("myGroups", JSON.parse('{!!$myGroupsJson!!}'));
    app.value("bill", null);
    app.constant('USER_REST', "{{URL::asset('api/usuario')}}/");
</script>

@stop

@section('content')
<div class="" ng-app="myApp" ng-controller="myCtrl">
    <div class="page-title">
        <div class="col-xs-12">
            <h3>Cadastro de despesa</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row" ng-show="step == 1">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Passo 1 - Informaçoes Gerais</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <h4>Informaçoes Gerais</h4>
                    <div class="form-vertical form-label-left">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                                <div class="form-group">
                                    <label>Nome da despesa*:</label>
                                    <input type="text" ng-model="bill.name" class="form-control has-feedback-left" placeholder="Nome da despesa">
                                    <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <label class="control-label">Grupo*:</label>
                                <select ng-model="groupSelected" ng-change="onGroupSelected()" ng-options="group.name for group in myGroups" class="form-control">
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Data:</label>
                                    <input type="date" ng-model="bill.date" class='form-control' />
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Data de Alerta:</label>
                                    <input type="date" ng-model="bill.alertDate" class='form-control' />
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <h4>Integrantes da Despesa</h4>
                    <div class="row">
                        <div class="form-vertical form-label-left">
                            <div class="col-md-12">
                                <label>Usuario</label>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" ng-model="username"/>
                                    </div>
                                    <div class="col-md-2 col-sm-6 col-xs-12">
                                        <button type="button" class="btn btn-primary btn-block" ng-click="searchUser()">Adicionar</button>
                                    </div>
                                    <% loadMsg %>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 col-xs-4" ng-repeat="member in bill.members">
                            <div class="checkbox">
                                <label><input type="checkbox"  ng-model="member.billParticipant"/> <%member.user.name%> (<%member.user.username%>)</label>
                            </div>
                        </div>    
                    </div>


                    <h5>Informaçao adicional</h5>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <textarea id="message"class="form-control" ng-model="bill.description"></textarea>
                        </div>
                    </div>
                    <div class='ln_solid'></div>
                    <div class='row'>
                        <div class="col-md-3 col-md-offset-9 col-xs-6 col-xs-offset-6">
                            <button class='btn btn-success btn-block' ng-click="validateFirstStep()">Proximo Passo</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row" ng-show="step == 2">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Passo 2 - Cadastro de Itens</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-vertical form-label-left">
                        <div class="row">
                            <div class="col-md-6 col-sm-4 col-xs-8 form-group has-feedback">
                                <div class="form-group">
                                    <label>Nome:</label>
                                    <input type="text" id="rItemName" ng-model="rItem.name" class="form-control has-feedback-left" placeholder="Nome do item">
                                    <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-3 col-xs-4">
                                <div class="form-group">
                                    <label>Quantidade:</label>
                                    <input type="number" ng-model="rItem.qt" class='form-control' step='1' />
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>Preço unitario:</label>
                                    <div class='row'>
                                        <div class='col-md-6 col-xs-8'>
                                            <input type="number" ng-model="rItem.price" class='form-control' step='0.01' ng-enter="addItem()" />
                                        </div>
                                        <div class='col-md-6 col-xs-4'>
                                            <button class='btn btn-primary btn-block' ng-click="addItem()">Adicionar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4>Integrantes do Item</h4>
                        <div class="row">
                            <div class="col-md-2 col-xs-4" ng-repeat="member in bill.members| filter: {billParticipant:true}">
                                <div class="checkbox">
                                    <label><input type="checkbox"  ng-model="member.itemParticipant"/> <%member.user.name%> (<%member.user.username%>)</label>
                                </div>
                            </div>    
                        </div>
                        <div class="row">
                            <table class='table table-striped col-md-12 col-xs-12 col-sm-12'>
                                <thead>
                                <th>Item</th>
                                <th>Preço Unitario x Quantidade</th>
                                <th>Distribuiçao</th>
                                <th>Remover</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="item in bill.items">
                                        <td class='col-md-5'>
                                            <input type="text" ng-model='item.name' class="form-control" />
                                        </td>
                                        <td class='col-md-3'>
                                            <div class='col-md-5'>
                                                <input type="number" ng-model="item.price" step = 0.01  class="form-control"/>
                                            </div>
                                            <div class='col-md-2' style="text-align: center;">
                                                X
                                            </div>
                                            <div class='col-md-5'>
                                                <input type="number" ng-model="item.qt" class="form-control"/>
                                            </div>    
                                        </td>
                                        <td class='col-md-2'>
                                            <div ng-if="!checkDistribution(item)">
                                                A distribuiçao esta incorreta
                                            </div>
                                            <div clas='col-md-12' ng-repeat="member in item.members">
                                                <label ><%member.user.name%> (<%member.user.username%>): </label>
                                                <input type="number" ng-model="member.distribution" step="0.01" class="form-control" />
                                            </div>
                                        </td>
                                        <td class='col-md-1'>
                                            <button type="button" class="btn btn-danger btn-sm btn-block" ng-click="removeElement(item, bill.items)">remover</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class='ln_solid'></div>
                        <div class='row'>
                            <div class="col-md-3 col-md-offset-6 col-xs-6">
                                <button class='btn btn-default btn-block' ng-click="setStep(1)">Voltar</button>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <button class='btn btn-success btn-block' ng-click="validateSecondStep()">Proximo Passo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row" ng-show="step == 3">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Passo 3 - Contribuintes</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-vertical form-label-left">
                        <b>Valor total: </b> R$ <% totalItems %>
                        <div class="row">
                            <div class="col-md-3 col-xs-12">
                                <label>Integrante</label>
                                <select ng-model="rContributor.member" class="form-control">
                                    <option ng-repeat="member in bill.members| filter: {billParticipant: true}" ng-value="member"><%member.user.name%> (<%member.user.username%>)</option>
                                </select>
                            </div>
                            <div class="col-md-8 col-xs-12">
                                <label>Valor</label>
                                <div class="row">
                                    <div class="col-md-3 col-xs-6">
                                        <input type="number" ng-model="rContributor.value" min="0" step="0.01" class="form-control" ng-enter="addContributor()" />
                                    </div>
                                    <div class="col-md-3 col-xs-6">
                                        <button type="button" class="btn btn-primary btn-block"ng-click="addContributor()">Adicionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class='table table-striped'>
                                <thead>
                                <th>Contribuidor</th>
                                <th>Valor</th>
                                <th>Remover</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="member in bill.members" ng-if="member.contributor">
                                        <td>
                                            <%member.user.name%> (<%member.user.username%>) 
                                        </td>
                                        <td> 
                                            <input type="number" class="form-control" ng-model="member.contribution" />                                            
                                        </td>
                                        <td>
                                            <button type="button" ng-click="removeContributor(member)" class="btn btn-danger btn-sm">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class='ln_solid'></div>
                    <div class='row'>
                        <div class="col-md-3 col-md-offset-6">
                            <button class='btn btn-default btn-block' ng-click="setStep(2)">Voltar</button>
                        </div>
                        <div class="col-md-3">
                            <form action="{{action("BillController@create")}}" id="billForm" method="post" onsubmit="return validateBillForm();" >
                                {{csrf_field()}}
                                <input type="hidden" value="<% bill %>" name="billJson" />
                                <button type="submit" class='btn btn-success btn-block'>Cadastrar Despesa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop