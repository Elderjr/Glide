ee<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            Glide - @yield('title')
        
        </title>
        <link href="{{URL::asset('/theme/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/theme/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/theme/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/theme/build/css/custom.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('css/pnotify.custom.min.css')}}" rel="stylesheet">
        @yield('cssImport')
    </head>
    <body class='nav-md'>
        <div class="container body">
            <div class="main_container">
                @include('shared.menu')
                <div class="right_col" role="main">
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
    <script src="{{URL::asset('js/pnotify.custom.min.js')}}"></script>
    <script>
        PNotify.prototype.options.styling = "bootstrap3";
        $(document).ready(function () {
            $('.ui-pnotify').remove();
            @if(session('feedback'))
                @if(session('feedback')->success != null)
                    new PNotify({
                        title: 'Notificaçao de sucesso',
                        text: '{{session('feedback')->success}}',
                        type: 'success'
                    });
                @endif
                @if(session('feedback')->alert != null)
                    new PNotify({
                        title: 'Notificaçao de erro',
                        text: '{{session('feedback')->error}}',
                        type: 'error'
                    });
                @endif
                @if(session('feedback')->error != null)
                    new PNotify({
                        title: 'Notificaçao de erro',
                        text: '{{session('feedback')->error}}',
                        type: 'error'
                    });
                @endif
            @endif
            @if(isset($feedback))
                @if($feedback->success != null)
                    new PNotify({
                        title: 'Notificaçao de sucesso',
                        text: '{{$feedback->success}}',
                        type: 'success'
                    });
                @endif
                @if($feedback->alert != null)
                    new PNotify({
                        title: 'Notificaçao de erro',
                        text: '{{$feedback->error}}',
                        type: 'error'
                    });
                @endif
                @if($feedback->error != null)
                    new PNotify({
                        title: 'Notificaçao de erro',
                        text: '{{$feedback->error}}',
                        type: 'error'
                    });
                @endif
            @endif

        });
    </script>
    @yield('jsImport')
</html>
