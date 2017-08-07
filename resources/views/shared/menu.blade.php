<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{action("GeneralController@index")}}" class="site_title"><i class="glyphicon glyphicon-send"></i> <span>Glide</span></a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <!-- <img src="images/img.jpg" alt="..." class="img-circle profile_img">-->
            </div>
            <div class="profile_info">
                <span>Bem vindo,</span>
                <h2>{{$generalInformation->user->name}}</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->
        <br />
        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <ul class="nav side-menu">
                    <li><a href='{{action("GeneralController@index")}}'><i class="fa fa-home"></i> Inicio</a></li>                    
                    <li><a><i class="fa fa-bar-chart-o"></i> Despesas <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href='{{action("BillController@index")}}'>Minhas Despesas</a></li>
                            <li><a href='{{action("BillController@create")}}'>Cadastrar Despesas</a></li>
                            <li><a href='{{action("BillController@pendingBills")}}'>Despesas Pendentes</a></li>                                            
                        </ul>
                    </li>
                    <li><a><i class="fa fa-group"></i> Grupos <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href='{{action("GroupController@create")}}'>Cadastrar Grupos</a></li>
                            <li><a href='{{action("GroupController@index")}}'>Gerenciador de Grupos</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-mail-reply"></i> Recebimentos <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{action("PaymentController@create")}}">Registrar Recebimento</a></li>
                            <li><a href="{{action("PaymentController@index")}}">Visualizar Recebimento</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-mail-forward"></i> Requerimentos <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{action("RequerimentController@create")}}">Registrar Requerimento</a></li>
                            <li><a href="{{action("RequerimentController@index")}}">Visualizar Requerimento</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
        <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
        </div>
        <!-- /menu footer buttons -->
    </div>
</div>

<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <!-- <img src="images/img.jpg" alt="">-->{{$generalInformation->user->name}} 
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <li><a href="{{action("GeneralController@profile")}}"> Editar Perfil</a></li>
                        <li><a href="{{action("GeneralController@profile")}}">Alterar Senha</a></li>
                        <li><a href="{{action("GeneralController@logout")}}"><i class="fa fa-sign-out pull-right"></i> Encerrar sessao</a></li>
                    </ul>
                </li>

                <li role="presentation" class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                        &nbsp;<i class="fa fa-warning"></i>
                        @if($generalInformation->totalWaitingRequirements > 0
                        && $generalInformation->totalAlertBills > 0)
                        <span class="badge bg-orange">2</span>
                        @elseif($generalInformation->totalWaitingRequirements > 0
                        || $generalInformation->totalAlertBills > 0)
                        <span class="badge bg-orange">1</span>
                        @endif
                    </a>
                    @if($generalInformation->totalWaitingRequirements > 0
                    || $generalInformation->totalAlertBills > 0)
                    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                        @if($generalInformation->totalAlertBills > 0)
                        <li>
                            <a>
                                Existem {{$generalInformation->totalAlertBills}} despesas em alerta. 
                            </a>
                        </li>
                        @endif
                        @if($generalInformation->totalWaitingRequirements > 0)
                        <li>
                            <a>
                                Existem {{$generalInformation->totalWaitingRequirements}} requerimentos em estado de espera
                            </a>
                        </li>
                        @endif
                    </ul>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->