<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Titulo Test</title>
        <!-- Bootstrap -->
        <link href="{{URL::asset('/theme/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="{{URL::asset('/theme/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
        <!-- iCheck -->
        <link href="{{URL::asset('/theme/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="{{URL::asset('/theme/build/css/custom.min.css')}}" rel="stylesheet">
        @yield('cssImport')
    </head>
    <body class='nav-md'>
        <div class="container body">
            <div class="main_container">
                @include('shared.menu')
                <div class="right_col" role="main">
                    @include('shared.feedback')
                    @yield('content')
                </div>
                <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        Glide - Gerenciador de Finanças. 
                        <a href="https://colorlib.com">GitHub</a>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
            </div>
        </div>
    </body>
    <!-- jQuery -->
    <script src="{{URL::asset('/theme/vendors/jquery/dist/jquery.min.js')}}"></script>
    <!-- Bootstrap -->
    <script src="{{URL::asset('/theme/vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <!-- FastClick -->
    <script src="{{URL::asset('/theme/vendors/fastclick/lib/fastclick.js')}}"></script>
    <!-- NProgress -->
    <script src="{{URL::asset('/theme/vendors/nprogress/nprogress.js')}}"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{URL::asset('/theme/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js')}}"></script>
    <!-- Custom Theme Scripts -->
    <script src="{{URL::asset('theme/build/js/custom.min.js')}}"></script>
    @yield('jsImport')
</html>
