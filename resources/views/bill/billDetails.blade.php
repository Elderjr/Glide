@extends('shared.layout')

@section('title') Detalhes da Despesa @stop

@section('jsImport')
<script src="{{URL::asset('js/angular.min.js')}}"></script>
<script src="{{URL::asset('js/decimal.min.js')}}"></script>
<script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});
app.controller('myCtrl', ['$scope', function ($scope) {
        $scope.bill = JSON.parse('{!!json_encode($bill)!!}');
        
        $scope.sub = function(a,b){
            return Decimal.sub(a,b).toNumber();
        }
    }]);</script>
@stop

@section('content')
<div class="" ng-app="myApp" ng-controller="myCtrl">
    <div class="page-title">
        <div class="title_left">
            <h3>Detalhes da Despesa</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Informaçoes de Pagamento</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-vertical form-label-left">
                        <ul>
                            <li ng-repeat="member in bill.members">
                                @{member.user.name} (@{member.user.username}) esta com saldo R$ @{member.paid} e
                                <span ng-if="member.paid > member.value">
                                    precisa receber R$ @{sub(member.paid,member.value)}
                                </span>
                                <span ng-if="member.paid < member.value">
                                   precisa pagar R$ @{sub(member.value, member.paid)}
                                </span>
                                <span ng-if="member.paid == member.value">
                                   esta quite
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Informaçoes Gerais</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-vertical form-label-left">
                        <div class="row">
                            <div class="col-md-5 col-sm-4 col-xs-4 form-group has-feedback">
                                <div class="form-group">
                                    <label>Nome da despesa</label>
                                    <span class="form-control">@{bill.name}</span>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label>Data</label>
                                    <span class="form-control">@{bill.date}</span>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label>Data de Alerta</label>
                                    <span class="form-control">@{bill.alertDate}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label>Grupo</label>
                                    <span class="form-control">@{bill.group.name}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5>Informaçao adicional</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                @{bill.description}
                            </p>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Itens</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-vertical form-label-left">
                        <table class='table table-striped'>
                            <thead>
                            <th>Item</th>
                            <th>Valor</th>
                            <th>Distribuiçao</th>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in bill.items">
                                    <td>
                                        @{item.name}
                                    </td>
                                    <td>
                                        @{item.price} x @{item.qt} (@{item.price * item.qt})
                                    </td>
                                    <td>
                                        <ul>
                                            <li ng-repeat="member in item.members">
                                                @{member.user.name} (@{member.user.username}): @{member.distribution}
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Contribuintes</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-6">
                            <table class='table table-striped'>
                                <thead>
                                <th>Contribuidor</th>
                                <th>Valor</th>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="member in bill.members" ng-if="member.contribution > 0">
                                        <td>
                                            @{member.user.name} (@{member.user.username}) 
                                        </td>
                                        <td> 
                                            @{member.contribution}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop