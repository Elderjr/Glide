@extends('shared.layout')
<?php $userId = $pageInfo->user->id; ?>
@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Meus Grupos</h3>
    </div>
    <div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
            <a href="{{action("GroupController@create")}}" class="btn btn-success btn-block">Cadastrar Grupo</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Meus Grupos</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="text-align: center;">
                <table class="table table-striped" width='30%'>
                    <thead>
                        <th>#</th>
                        <th>Grupo</th>
                        <th>Detalhes</th>
                        <th>Sair</th>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        @foreach($pageInfo->myGroups as $group)
                        <tr>
                            <td>{{$count}}</td>
                            <td>
                                <a href="{{action("GroupController@show", $group-> id)}}">{{$group-> name}}</a>
                                @if($group->getMemberById($userId)->admin)
                                <span>
                                    (administrador)
                                </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{action("GroupController@show", $group->id)}}" class="btn btn-sm btn-primary" >detalhes</a>
                            </td>
                            <td>
                                <a href="{{action("GroupController@leaveGroup", $group-> id)}}" class="btn btn-danger btn-sm"> Sair </a>
                            </td>
                        </tr>
                        <?php $count++;?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
@stop