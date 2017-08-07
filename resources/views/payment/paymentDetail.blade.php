@extends('shared.layout')

@section('title') Detalhes do Recebimento @stop

@section('content')

<div class="">
    <div class="page-title">
        <div class="title_left">
            <h3>Detalhes de Recebimento</h3>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">

                    <h2>Pagamento de {{$payment->payerUser->name}} para {{$payment->receiverUser->name}}</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Settings 1</a>
                                </li>
                                <li><a href="#">Settings 2</a>
                                </li>
                            </ul>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped" width='30%'>
                        <thead>
                        <th>#</th>
                        <th>Despesa</th>
                        <th>Recebimento</th>
                        </thead>
                        <tbody>
                            <?php $count = 1; ?>
                            @foreach($payment->paymentBills as $paymentBill)
                            <tr>
                                <td>{{$count}}</td>
                                <td>{{$paymentBill->bill->name}}</td>
                                <td>R$ {{$paymentBill->value}}</td>
                            </tr>
                            <?php $count++; ?>
                            @endforeach
                        </tbody>
                    </table>
                    <b>Total:</b> R$ {{$payment->value}}
                    @if($payment->description != null)
                    <h5>Informaçao adicional</h5>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                {{$payment->description}}
                            </p>
                        </div>
                    </div>
                    @endif
                    <br/>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-9">
                            <a href="{{action("PaymentController@rollback", $payment->id)}}" class='btn btn-danger btn-block'>Reverter Recebimento</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop