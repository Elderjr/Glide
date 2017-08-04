@extends('shared.layout')

@section('jsImport')
@if (isset($pageInfo))
<script src="{{URL::asset('js/angular.min.js')}}"></script>
<script src="{{URL::asset('js/decimal.min.js')}}"></script>
<script>
var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('@{');
    $interpolateProvider.endSymbol('}');
});
app.controller("myCtrl", function ($scope) {
    $scope.pageInfo = JSON.parse('{!!json_encode($pageInfo)!!}');
});
</script>
@endif
@stop

@section('content')
<div class='row'>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <form class="form-horizontal form-label-left">
                <div class="row">
                    <div class="col-md-12">
                        <label>Username do usuario</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" class="form-control has-feedback-left" id="username" name="username" placeholder="Username do usuário">
                                    <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-block" value="Buscar" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @if(isset($pageInfo))
            <div ng-app="myApp" ng-controller="myCtrl">
                <hr/>
                @if(count($pageInfo->billsInDebt) == 0)
                <h4>Não existem dívidas com @{pageInfo.destinationUser.name}</h4>
                @else
                <h4>Dívidas com @{pageInfo.destinationUser.name}</h4>
                <div class="x_content">
                    <table class="table table-striped" width='30%'>
                        <thead>
                        <th>#</th>
                        <th>Despesa</th>
                        <th>Dívida</th>
                        </thead>
                        <tbody>
                            <tr ng-repeat="bill in pageInfo.billsInDebt">
                                <td>@{pageInfo.billsInDebt.indexOf(bill) + 1}</td>
                                <td>@{bill.name}</td>
                                <td>@{bill.debt}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <b>Total:</b> R$ @{pageInfo.total}
                <hr/>
                <form class="form-horizontal form-label-left" action="{{action("RequerimentController@store")}}" method="post">
                    {{ csrf_field()}}
                    <input type="hidden" name="destinationUserId" value="@{pageInfo.destinationUser.id}" />
                    <div class="row">
                        <div class="col-md-12">
                            <label>Valor do Requerimento</label>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="number" class="form-control" name="requirementValue" min="0" step="0.01" ng-model="requirementValue" />
                                    </div>
                                </div>
                                <div ng-if="requirementValue > pageInfo.total" class="col-md-10">
                                    Atençao, valor maior que total
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label>Informaçao adicional</label>
                            <textarea id="message"class="form-control" name="requirementDescription"></textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-9">
                            <input type="submit" value="Registrar Pagamento" class="btn btn-success btn-block" />
                        </div>
                    </div>
                </form>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@stop