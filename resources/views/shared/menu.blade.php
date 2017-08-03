<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>Glide</span></a>
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
                <h3>General</h3>
                <ul class="nav side-menu">
                    <li><a href='{{action("GeneralController@index")}}'><i class="fa fa-home"></i> Inicio</a></li>
                    <li><a href='{{action("HistoryController@index")}}'><i class="fa fa-home"></i> Historico</a></li>
                    <li><a><i class="fa fa-edit"></i> Despesas <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href='{{action("BillController@create")}}'>Cadastrar Despesas</a></li>
                            <li><a href='{{action("BillController@pendingBills")}}'>Despesas Pendentes</a></li>                                            
                        </ul>
                    </li>
                    <li><a><i class="fa fa-desktop"></i> Grupos <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href='{{action("GroupController@create")}}'>Cadastrar Grupos</a></li>
                            <li><a href='{{action("GroupController@index")}}'>Gerenciador de Grupos</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-table"></i> Pagamentos <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{action("PaymentController@create")}}">Registrar Pagamento</a></li>
                            <li><a href="{{action("PaymentController@index")}}">Visualizar Pagamentos</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-bar-chart-o"></i> Requerimentos<span class="fa fa-chevron-down"></span></a>
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
                        <li><a href="javascript:;"> Editar Perfil</a></li>
                        <li><a href="javascript:;">Alterar Senha</a></li>
                        <li><a href="login.html"><i class="fa fa-sign-out pull-right"></i> Encerrar sessao</a></li>
                    </ul>
                </li>

                <li role="presentation" class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                        &nbsp;<i class="fa fa-envelope-o"></i>
                        <span class="badge bg-green">6</span>
                    </a>
                    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                        <li>
                            <a>
                                <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                                <span>
                                    <span>John Smith</span>
                                    <span class="time">3 mins ago</span>
                                </span>
                                <span class="message">
                                    Film festivals used to be do-or-die moments for movie makers. They were where...
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->