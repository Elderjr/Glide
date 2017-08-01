@extends('shared.layout')
<?php $userId = $pageInfo->user->id; ?>
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>{{$pageInfo->group->name}}</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="text-align: center;">
                <form class="form-horizontal form-label-left" action="{{action("GroupController@store")}}" method="post">
                    <div class='row'>
                        <div class='col-md-6 col-md-offset-3'>
                            <table class="table table-striped" width='30%'>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Membro</th>
                                        @if($pageInfo->group->getMemberById($userId)->admin)
                                        <th>Tornar Administrador</th>
                                        <th>Remover</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $count = 1; ?>
                                    @foreach($pageInfo->group->members as $member)
                                    <tr>
                                        <td>{{$count}}</td>
                                        <td>
                                            {{$member->user->name}}
                                            @if($member->admin)
                                            <span class="badge bg-blue">Administrador</span>
                                            @endif
                                        </td>
                                        @if($pageInfo->group->getMemberById($userId)->admin)
                                        <td>
                                            <input type="checkbox" class="flat" value="{{$member->user->id}}" name="turnAdmin[]" />
                                        </td>
                                        <td>
                                            <input type="checkbox" class="flat" value="{{$member->user->id}}" name="removeMember[]" />
                                        </td>
                                        @endif
                                    </tr>
                                    <?php $count++; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-md-offset-5">
                            <button class="btn btn-block btn-success">Salvar</button>
                        </div>
                    </div>
                    <div class="divider"></div>

                </form>


            </div>
            @if($pageInfo->group->getMemberById($userId)->admin)
            <form action="{{action('GroupController@storeMember', $pageInfo->group->id)}}" method="post">
                {{ csrf_field()}}
                <input type="text" name="username"/>
                <button>Adicionar</button>
            </form>
            @endif
        </div>
    </div>
</div>
<div class="clearfix"></div>
</div>
@stop

