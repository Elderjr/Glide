@extends ('shared.layout')                

@section('title') Detalhes de Requerimento @stop

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Requerimento</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <h4>Dados Gerais</h4>

                <div class="row">
                    <div class="col-md-2" style="font-size: 15px;">
                        <b>Estado: </b>
                        @if($requirement->status == "accepted")
                        <span class="badge bg-green">Aceito</span>
                        @elseif($requirement->status == "rejected")
                        <span class="badge bg-red">Rejeitado</span>
                        @else
                        <span class="badge bg-orange">Aguardando</span>
                        @endif
                    </div>
                    <div class="col-md-3" style="font-size: 15px;">
                        <b>Remetente: </b>{{$requirement->sourceUser->toString()}}
                    </div>
                    <div class="col-md-3" style="font-size: 15px;">
                        <b>Destinatario: </b> {{$requirement->destinationUser->toString()}}
                    </div>
                    <div class="col-md-3" style="font-size: 15px;">
                        <b>Valor: </b> R$ {{$requirement->value}}
                    </div>
                </div>
                <div class="ln_solid"></div>
                <h4 class="heading">Detalhes</h4>
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                            {{$requirement->description}}
                        </p>    
                    </div>
                </div>
                @if($requirement->status == 'waiting'
                    && $requirement->destinationUser->id == Illuminate\Support\Facades\Auth::user()->id)
                <div class="ln_solid"></div>
                <div style="text-align: right;">
                    <button class='btn btn-danger btn-sm'>Cancelar</button>
                    <button class="btn btn-success btn-sm">Confirmar</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop